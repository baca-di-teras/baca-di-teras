<?php
<<<<<<< HEAD
/**
 * Migrasi data 4 perpustakaan sekolah ke database utama Baca Di Teras.
 *
 * Script ini sengaja berada di custom/scripts agar tidak mengubah core SLiMS.
 * Jalankan setelah database staging source sudah diimport:
 * - temp_sdbanjarsari
 * - temp_smpn1teras
 * - temp_sdn2teras
 * - temp_smpn2teras
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Script ini hanya boleh dijalankan lewat CLI.\n");
    exit(1);
}

require_once __DIR__ . '/../config/database.php';

const GENERATED_AUTHOR_START = 800000;
const GENERATED_PUBLISHER_START = 850000;
const GENERATED_TOPIC_START = 900000;
const MIGRATION_BLOCK_SIZE = 100000;

$sources = [
    [
        'key' => 'sdbanjarsari',
        'type' => 'slims',
        'db' => 'temp_sdbanjarsari',
        'code' => 'SBJ',
        'offset' => 100000,
        'name' => 'SD Banjarsari',
        'slug' => 'sd-banjarsari',
        'badge' => 'Sekolah Dasar',
        'address' => 'SD Banjarsari, Teras, Boyolali',
        'image' => '/custom/assets/images/library-1.png',
    ],
    [
        'key' => 'sdn2teras',
        'type' => 'inlis',
        'db' => 'temp_sdn2teras',
        'code' => 'SD2',
        'offset' => 200000,
        'name' => 'SD Negeri 2 Teras',
        'slug' => 'sd-negeri-2-teras',
        'badge' => 'Sekolah Dasar',
        'address' => 'SD Negeri 2 Teras, Boyolali',
        'image' => '/custom/assets/images/library-2.png',
    ],
    [
        'key' => 'smpn1teras',
        'type' => 'slims',
        'db' => 'temp_smpn1teras',
        'code' => 'S1T',
        'offset' => 300000,
        'name' => 'SMP Negeri 1 Teras',
        'slug' => 'smp-negeri-1-teras',
        'badge' => 'Sekolah Menengah',
        'address' => 'SMP Negeri 1 Teras, Boyolali',
        'image' => '/custom/assets/images/library-3.png',
    ],
    [
        'key' => 'smpn2teras',
        'type' => 'inlis',
        'db' => 'temp_smpn2teras',
        'code' => 'S2T',
        'offset' => 400000,
        'name' => 'SMP Negeri 2 Teras',
        'slug' => 'smp-negeri-2-teras',
        'badge' => 'Sekolah Menengah',
        'address' => 'SMP Negeri 2 Teras, Boyolali',
        'image' => '/custom/assets/images/library-detail-hero.png',
    ],
];

$target = connectDatabase(DB_NAME);
$root = connectDatabase(null);

try {
    assertRequiredDatabases($root, array_column($sources, 'db'));

    $target->exec('SET FOREIGN_KEY_CHECKS = 0');
    ensureReferenceData($target, $sources);
    ensureMigrationMapTable($target);
    validateSourceIdRanges($sources);
    clearPreviousMigration($target, $sources);
    syncLibraryRecords($target, $sources);
    $sources = attachLibraryIds($target, $sources);

    $summary = [];
    $generatedMaps = [
        'authors' => [],
        'publishers' => [],
        'topics' => [],
        'nextAuthorId' => GENERATED_AUTHOR_START,
        'nextPublisherId' => GENERATED_PUBLISHER_START,
        'nextTopicId' => GENERATED_TOPIC_START,
    ];

    foreach ($sources as $source) {
        $sourceDb = connectDatabase($source['db']);
        if ($source['type'] === 'slims') {
            $summary[$source['key']] = migrateSlimsSource($target, $sourceDb, $source);
            continue;
        }

        $summary[$source['key']] = migrateInlisSource($target, $sourceDb, $source, $generatedMaps);
    }

    syncLibraryTotals($target, $sources);
    $verification = buildVerification($target, $sources);
    $target->exec('SET FOREIGN_KEY_CHECKS = 1');

    printSummary($summary, $verification);
} catch (Throwable $exception) {
    $target->exec('SET FOREIGN_KEY_CHECKS = 1');
    fwrite(STDERR, "\nMigrasi gagal: " . $exception->getMessage() . "\n");
    exit(1);
}

function connectDatabase(?string $database): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;
    if ($database !== null && $database !== '') {
        $dsn .= ';dbname=' . $database;
    }

    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function assertRequiredDatabases(PDO $root, array $databaseNames): void
{
    $statement = $root->prepare('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
    foreach ($databaseNames as $databaseName) {
        $statement->execute([$databaseName]);
        if (!$statement->fetchColumn()) {
            throw new RuntimeException("Database staging {$databaseName} belum tersedia.");
        }
    }
}

function ensureReferenceData(PDO $target, array $sources): void
{
    $today = date('Y-m-d');

    $statusRows = [
        ['AVL', 'Available', null, 0, 0],
        ['LO', 'On Loan', null, 1, 0],
        ['WD', 'Withdrawn', null, 1, 1],
        ['MIS', 'Missing', null, 1, 1],
        ['NL', 'No Loan', 'a:1:{i:0;s:1:"1";}', 1, 1],
        ['R', 'Repair', 'a:1:{i:0;s:1:"1";}', 1, 1],
    ];

    $statusInsert = $target->prepare(
        'INSERT INTO mst_item_status
            (item_status_id, item_status_name, rules, no_loan, skip_stock_take, input_date, last_update)
         VALUES (?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            item_status_name = VALUES(item_status_name),
            rules = VALUES(rules),
            no_loan = VALUES(no_loan),
            skip_stock_take = VALUES(skip_stock_take),
            last_update = VALUES(last_update)'
    );

    foreach ($statusRows as $row) {
        $statusInsert->execute([$row[0], $row[1], $row[2], $row[3], $row[4], $today, $today]);
    }

    $locationInsert = $target->prepare(
        'INSERT INTO mst_location (location_id, location_name, input_date, last_update)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE location_name = VALUES(location_name), last_update = VALUES(last_update)'
    );

    foreach ($sources as $source) {
        $locationInsert->execute([$source['code'], $source['name'], $today, $today]);
    }
}

function ensureMigrationMapTable(PDO $target): void
{
    $target->exec(
        'CREATE TABLE IF NOT EXISTS bdt_migration_map (
            map_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            library_id INT(11) NOT NULL,
            source_code VARCHAR(10) NOT NULL,
            source_database VARCHAR(64) NOT NULL,
            source_type VARCHAR(20) NOT NULL,
            source_table VARCHAR(64) NOT NULL,
            old_id VARCHAR(100) NOT NULL,
            target_table VARCHAR(64) NOT NULL,
            new_id VARCHAR(100) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (map_id),
            UNIQUE KEY uniq_bdt_migration_source_row (source_code, source_table, old_id, target_table),
            KEY idx_bdt_migration_library (library_id),
            KEY idx_bdt_migration_source (source_code),
            KEY idx_bdt_migration_target (target_table, new_id),
            CONSTRAINT fk_bdt_migration_library
                FOREIGN KEY (library_id) REFERENCES bdt_library (library_id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function validateSourceIdRanges(array $sources): void
{
    foreach ($sources as $source) {
        $sourceDb = connectDatabase($source['db']);

        if ($source['type'] === 'slims') {
            assertIdsFitMigrationBlock($sourceDb, $source, 'biblio', 'biblio_id');
            assertIdsFitMigrationBlock($sourceDb, $source, 'item', 'item_id');
            assertIdsFitMigrationBlock($sourceDb, $source, 'loan', 'loan_id');
            assertIdsFitMigrationBlock($sourceDb, $source, 'mst_author', 'author_id');
            assertIdsFitMigrationBlock($sourceDb, $source, 'mst_publisher', 'publisher_id');
            assertIdsFitMigrationBlock($sourceDb, $source, 'mst_topic', 'topic_id');
            continue;
        }

        assertIdsFitMigrationBlock($sourceDb, $source, 'catalogs', 'ID');
        assertIdsFitMigrationBlock($sourceDb, $source, 'collections', 'ID');
        assertIdsFitMigrationBlock($sourceDb, $source, 'members', 'ID');
        if (tableExists($sourceDb, 'collectionloanitems')) {
            assertIdsFitMigrationBlock($sourceDb, $source, 'collectionloanitems', 'ID');
        }
    }
}

function assertIdsFitMigrationBlock(PDO $sourceDb, array $source, string $table, string $column): void
{
    if (!tableExists($sourceDb, $table)) {
        return;
    }

    $statement = $sourceDb->query("SELECT MAX(`{$column}`) FROM `{$table}`");
    $maxId = (int) ($statement->fetchColumn() ?: 0);
    if ($maxId >= MIGRATION_BLOCK_SIZE) {
        $start = (int) $source['offset'];
        $end = $start + MIGRATION_BLOCK_SIZE - 1;
        throw new RuntimeException(
            "ID {$source['db']}.{$table}.{$column} mencapai {$maxId}, melebihi jatah block {$start}-{$end}."
        );
    }
}

function clearPreviousMigration(PDO $target, array $sources): void
{
    $codes = array_column($sources, 'code');
    $prefixConditions = implode(' OR ', array_fill(0, count($codes), 'member_id LIKE ?'));
    $prefixParams = array_map(static fn(string $code): string => $code . '-%', $codes);

    if (tableExists($target, 'bdt_migration_map')) {
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $statement = $target->prepare("DELETE FROM bdt_migration_map WHERE source_code IN ({$placeholders})");
        $statement->execute($codes);
    }

    $target->exec('DELETE FROM search_biblio WHERE biblio_id >= 100000');
    $target->exec('DELETE FROM biblio_topic WHERE biblio_id >= 100000 OR topic_id >= 100000');
    $target->exec('DELETE FROM biblio_author WHERE biblio_id >= 100000 OR author_id >= 100000');
    $target->exec('DELETE FROM item WHERE item_id >= 100000');
    $target->exec('DELETE FROM loan WHERE loan_id >= 100000');
    $target->exec('DELETE FROM biblio WHERE biblio_id >= 100000');
    $target->exec('DELETE FROM mst_author WHERE author_id >= 100000');
    $target->exec('DELETE FROM mst_publisher WHERE publisher_id >= 100000');
    $target->exec('DELETE FROM mst_topic WHERE topic_id >= 100000');

    $statement = $target->prepare("DELETE FROM member WHERE {$prefixConditions}");
    $statement->execute($prefixParams);
}

function attachLibraryIds(PDO $target, array $sources): array
{
    $statement = $target->prepare('SELECT library_id FROM bdt_library WHERE slims_location_id = ? LIMIT 1');

    foreach ($sources as $index => $source) {
        $statement->execute([$source['code']]);
        $libraryId = $statement->fetchColumn();
        if ($libraryId === false) {
            throw new RuntimeException("Entity perpustakaan {$source['code']} tidak ditemukan di bdt_library.");
        }

        $sources[$index]['library_id'] = (int) $libraryId;
    }

    return $sources;
}

function syncLibraryRecords(PDO $target, array $sources): void
{
    $target->exec("UPDATE bdt_library SET status = 'nonaktif' WHERE slims_location_id NOT IN ('SBJ', 'SD2', 'S1T', 'S2T')");

    $select = $target->prepare('SELECT library_id FROM bdt_library WHERE slug = ? LIMIT 1');
    $insert = $target->prepare(
        'INSERT INTO bdt_library
            (slims_location_id, slug, name, tagline, badge, status, address, village, cover_image,
             thumbnail_image, total_koleksi, total_anggota, description, sort_order)
         VALUES (?, ?, ?, ?, ?, "aktif", ?, "Teras", ?, ?, 0, 0, ?, ?)'
    );
    $update = $target->prepare(
        'UPDATE bdt_library
         SET slims_location_id = ?,
             name = ?,
             tagline = ?,
             badge = ?,
             status = "aktif",
             address = ?,
             village = "Teras",
             cover_image = ?,
             thumbnail_image = ?,
             description = ?,
             sort_order = ?,
             updated_at = NOW()
         WHERE library_id = ?'
    );

    foreach ($sources as $index => $source) {
        $tagline = 'Koleksi perpustakaan sekolah yang terintegrasi ke portal Baca Di Teras';
        $description = 'Data koleksi berasal dari migrasi database ' . strtoupper($source['type']) . ' sekolah.';

        $select->execute([$source['slug']]);
        $libraryId = $select->fetchColumn();

        if ($libraryId) {
            $update->execute([
                $source['code'],
                $source['name'],
                $tagline,
                $source['badge'],
                $source['address'],
                $source['image'],
                $source['image'],
                $description,
                $index + 1,
                (int) $libraryId,
            ]);
            syncDefaultLibraryHours($target, (int) $libraryId);
            continue;
        }

        $insert->execute([
            $source['code'],
            $source['slug'],
            $source['name'],
            $tagline,
            $source['badge'],
            $source['address'],
            $source['image'],
            $source['image'],
            $description,
            $index + 1,
        ]);
        syncDefaultLibraryHours($target, (int) $target->lastInsertId());
    }
}

function syncDefaultLibraryHours(PDO $target, int $libraryId): void
{
    $target->prepare('DELETE FROM bdt_library_hour WHERE library_id = ?')->execute([$libraryId]);
    $insert = $target->prepare(
        'INSERT INTO bdt_library_hour (library_id, day_of_week, is_open, open_time, close_time)
         VALUES (?, ?, ?, ?, ?)'
    );

    for ($day = 0; $day <= 6; $day++) {
        $isOpen = $day === 0 ? 0 : 1;
        $openTime = $isOpen ? '08:00:00' : null;
        $closeTime = $isOpen ? ($day === 6 ? '13:00:00' : '16:00:00') : null;
        $insert->execute([$libraryId, $day, $isOpen, $openTime, $closeTime]);
    }
}

function migrateSlimsSource(PDO $target, PDO $sourceDb, array $source): array
{
    $summary = [
        'biblio' => 0,
        'item' => 0,
        'member' => 0,
        'loan' => 0,
        'loanSkipped' => 0,
        'author' => 0,
        'publisher' => 0,
        'topic' => 0,
    ];

    $offset = (int) $source['offset'];
    $authorIdMap = [];
    $publisherIdMap = [];
    $topicIdMap = [];

    foreach ($sourceDb->query('SELECT * FROM mst_author ORDER BY author_id') as $row) {
        $oldAuthorId = (int) $row['author_id'];
        $newAuthorId = ensureSlimsAuthor($target, [
            'author_id' => $offset + $oldAuthorId,
            'author_name' => limitText(cleanText($row['author_name'] ?? ''), 100, 'Tidak diketahui'),
            'author_year' => limitText(cleanText($row['author_year'] ?? null), 20),
            'authority_type' => validAuthorityType($row['authority_type'] ?? null),
            'auth_list' => limitText(cleanText($row['auth_list'] ?? null), 20),
            'input_date' => dateValue($row['input_date'] ?? null, date('Y-m-d')),
            'last_update' => dateValue($row['last_update'] ?? null),
        ]);
        $authorIdMap[$oldAuthorId] = $newAuthorId;
        recordMigrationMap($target, $source, 'mst_author', $oldAuthorId, 'mst_author', $newAuthorId);
        $summary['author']++;
    }

    foreach ($sourceDb->query('SELECT * FROM mst_publisher ORDER BY publisher_id') as $row) {
        $oldPublisherId = (int) $row['publisher_id'];
        $newPublisherId = ensureSlimsPublisher($target, [
            'publisher_id' => $offset + $oldPublisherId,
            'publisher_name' => limitText(cleanText($row['publisher_name'] ?? ''), 100, 'Tidak diketahui'),
            'input_date' => dateValue($row['input_date'] ?? null),
            'last_update' => dateValue($row['last_update'] ?? null),
        ]);
        $publisherIdMap[$oldPublisherId] = $newPublisherId;
        recordMigrationMap($target, $source, 'mst_publisher', $oldPublisherId, 'mst_publisher', $newPublisherId);
        $summary['publisher']++;
    }

    foreach ($sourceDb->query('SELECT * FROM mst_topic ORDER BY topic_id') as $row) {
        $oldTopicId = (int) $row['topic_id'];
        $newTopicId = ensureSlimsTopic($target, [
            'topic_id' => $offset + $oldTopicId,
            'topic' => limitText(cleanText($row['topic'] ?? ''), 50, 'Tidak diketahui'),
            'topic_type' => validTopicType($row['topic_type'] ?? null),
            'auth_list' => limitText(cleanText($row['auth_list'] ?? null), 20),
            'classification' => limitText(cleanText($row['classification'] ?? ''), 50, ''),
            'input_date' => dateValue($row['input_date'] ?? null),
            'last_update' => dateValue($row['last_update'] ?? null),
        ]);
        $topicIdMap[$oldTopicId] = $newTopicId;
        recordMigrationMap($target, $source, 'mst_topic', $oldTopicId, 'mst_topic', $newTopicId);
        $summary['topic']++;
    }

    foreach ($sourceDb->query('SELECT * FROM biblio ORDER BY biblio_id') as $row) {
        $oldBiblioId = (int) $row['biblio_id'];
        $newBiblioId = $offset + $oldBiblioId;
        $publisherId = nullableInt($row['publisher_id'] ?? null);
        insertRow($target, 'biblio', [
            'biblio_id' => $newBiblioId,
            'gmd_id' => nullableInt($row['gmd_id'] ?? null),
            'title' => cleanText($row['title'] ?? 'Tanpa Judul') ?: 'Tanpa Judul',
            'sor' => limitText(cleanText($row['sor'] ?? null), 200),
            'edition' => limitText(cleanText($row['edition'] ?? null), 50),
            'isbn_issn' => limitText(cleanText($row['isbn_issn'] ?? null), 32),
            'publisher_id' => $publisherId ? ($publisherIdMap[$publisherId] ?? null) : null,
            'publish_year' => limitText(cleanText($row['publish_year'] ?? null), 20),
            'collation' => limitText(cleanText($row['collation'] ?? null), 100),
            'series_title' => limitText(cleanText($row['series_title'] ?? null), 200),
            'call_number' => limitText(cleanText($row['call_number'] ?? null), 50),
            'language_id' => limitText(cleanText($row['language_id'] ?? 'id'), 5, 'id'),
            'source' => limitText($source['code'], 10),
            'publish_place_id' => null,
            'classification' => limitText(cleanText($row['classification'] ?? null), 40),
            'notes' => cleanText($row['notes'] ?? null),
            'image' => limitText(cleanText($row['image'] ?? null), 100),
            'file_att' => limitText(cleanText($row['file_att'] ?? null), 255),
            'opac_hide' => (int) ($row['opac_hide'] ?? 0),
            'promoted' => (int) ($row['promoted'] ?? 0),
            'labels' => cleanText($row['labels'] ?? null),
            'frequency_id' => (int) ($row['frequency_id'] ?? 0),
            'spec_detail_info' => cleanText($row['spec_detail_info'] ?? null),
            'content_type_id' => nullableInt($row['content_type_id'] ?? null),
            'media_type_id' => nullableInt($row['media_type_id'] ?? null),
            'carrier_type_id' => nullableInt($row['carrier_type_id'] ?? null),
            'input_date' => dateTimeValue($row['input_date'] ?? null),
            'last_update' => dateTimeValue($row['last_update'] ?? null),
            'uid' => nullableInt($row['uid'] ?? null),
        ]);
        recordMigrationMap($target, $source, 'biblio', $oldBiblioId, 'biblio', $newBiblioId);
        $summary['biblio']++;
    }

    foreach ($sourceDb->query('SELECT * FROM biblio_author ORDER BY biblio_id, author_id') as $row) {
        $oldAuthorId = (int) $row['author_id'];
        insertRow($target, 'biblio_author', [
            'biblio_id' => $offset + (int) $row['biblio_id'],
            'author_id' => $authorIdMap[$oldAuthorId] ?? ($offset + $oldAuthorId),
            'level' => (int) ($row['level'] ?? 1),
        ], true);
    }

    foreach ($sourceDb->query('SELECT * FROM biblio_topic ORDER BY biblio_id, topic_id') as $row) {
        $oldTopicId = (int) $row['topic_id'];
        insertRow($target, 'biblio_topic', [
            'biblio_id' => $offset + (int) $row['biblio_id'],
            'topic_id' => $topicIdMap[$oldTopicId] ?? ($offset + $oldTopicId),
            'level' => (int) ($row['level'] ?? 1),
        ], true);
    }

    $itemCodeMap = [];
    foreach ($sourceDb->query('SELECT * FROM item ORDER BY item_id') as $row) {
        $itemId = (int) $row['item_id'];
        $newItemId = $offset + $itemId;
        $oldItemCode = (string) ($row['item_code'] ?? '');
        $newItemCode = makeItemCode($source['code'], $oldItemCode, $itemId);
        if ($oldItemCode !== '' && !isset($itemCodeMap[$oldItemCode])) {
            $itemCodeMap[$oldItemCode] = $newItemCode;
        }

        insertRow($target, 'item', [
            'item_id' => $newItemId,
            'biblio_id' => nullableInt($row['biblio_id'] ?? null) ? $offset + (int) $row['biblio_id'] : null,
            'call_number' => limitText(cleanText($row['call_number'] ?? null), 50),
            'coll_type_id' => nullableInt($row['coll_type_id'] ?? null),
            'item_code' => $newItemCode,
            'inventory_code' => limitText(cleanText($row['inventory_code'] ?? null), 200),
            'received_date' => dateValue($row['received_date'] ?? null),
            'supplier_id' => limitText(cleanText($row['supplier_id'] ?? null), 6),
            'order_no' => limitText(cleanText($row['order_no'] ?? null), 20),
            'location_id' => $source['code'],
            'order_date' => dateValue($row['order_date'] ?? null),
            'item_status_id' => mapSlimsItemStatus($row['item_status_id'] ?? null),
            'site' => limitText(cleanText($row['site'] ?? null), 50),
            'source' => (int) ($row['source'] ?? 0),
            'invoice' => limitText(cleanText($row['invoice'] ?? null), 20),
            'price' => nullableInt($row['price'] ?? null),
            'price_currency' => limitText(cleanText($row['price_currency'] ?? null), 10),
            'invoice_date' => dateValue($row['invoice_date'] ?? null),
            'input_date' => dateTimeValue($row['input_date'] ?? null, date('Y-m-d H:i:s')),
            'last_update' => dateTimeValue($row['last_update'] ?? null),
            'uid' => nullableInt($row['uid'] ?? null),
        ]);
        recordMigrationMap($target, $source, 'item', $itemId, 'item', $newItemId);
        $summary['item']++;
    }

    $memberMap = [];
    foreach ($sourceDb->query('SELECT * FROM member ORDER BY member_id') as $row) {
        $oldMemberId = (string) $row['member_id'];
        $newMemberId = makeMemberId($source['code'], $oldMemberId);
        $memberMap[$oldMemberId] = $newMemberId;

        insertRow($target, 'member', [
            'member_id' => $newMemberId,
            'member_name' => limitText(cleanText($row['member_name'] ?? ''), 100, 'Tanpa Nama'),
            'gender' => (int) ($row['gender'] ?? 0),
            'birth_date' => dateValue($row['birth_date'] ?? null),
            'member_type_id' => nullableInt($row['member_type_id'] ?? null) ?: 1,
            'member_address' => limitText(cleanText($row['member_address'] ?? null), 255),
            'member_mail_address' => limitText(cleanText($row['member_mail_address'] ?? null), 255),
            'member_email' => limitText(cleanText($row['member_email'] ?? null), 100),
            'postal_code' => limitText(cleanText($row['postal_code'] ?? null), 20),
            'inst_name' => limitText(cleanText($row['inst_name'] ?? $source['name']), 100),
            'is_new' => nullableInt($row['is_new'] ?? null),
            'member_image' => limitText(cleanText($row['member_image'] ?? null), 200),
            'pin' => limitText(cleanText($row['pin'] ?? null), 50),
            'member_phone' => limitText(cleanText($row['member_phone'] ?? null), 50),
            'member_fax' => limitText(cleanText($row['member_fax'] ?? null), 50),
            'member_since_date' => dateValue($row['member_since_date'] ?? null),
            'register_date' => dateValue($row['register_date'] ?? null),
            'expire_date' => dateValue($row['expire_date'] ?? null, '2099-12-31'),
            'member_notes' => cleanText($row['member_notes'] ?? null),
            'is_pending' => (int) ($row['is_pending'] ?? 0),
            'mpasswd' => limitText(cleanText($row['mpasswd'] ?? null), 64),
            'last_login' => dateTimeValue($row['last_login'] ?? null),
            'last_login_ip' => limitText(cleanText($row['last_login_ip'] ?? null), 50),
            'input_date' => dateValue($row['input_date'] ?? null),
            'last_update' => dateValue($row['last_update'] ?? null),
        ]);
        recordMigrationMap($target, $source, 'member', $oldMemberId, 'member', $newMemberId);
        $summary['member']++;
    }

    foreach ($sourceDb->query('SELECT * FROM loan ORDER BY loan_id') as $row) {
        $oldLoanId = (int) $row['loan_id'];
        $newLoanId = $offset + $oldLoanId;
        $oldItemCode = (string) ($row['item_code'] ?? '');
        $oldMemberId = (string) ($row['member_id'] ?? '');

        if (!isset($itemCodeMap[$oldItemCode]) || !isset($memberMap[$oldMemberId])) {
            $summary['loanSkipped']++;
            continue;
        }

        insertRow($target, 'loan', [
            'loan_id' => $newLoanId,
            'item_code' => $itemCodeMap[$oldItemCode],
            'member_id' => $memberMap[$oldMemberId],
            'loan_date' => dateValue($row['loan_date'] ?? null, date('Y-m-d')),
            'due_date' => dateValue($row['due_date'] ?? null, date('Y-m-d')),
            'renewed' => (int) ($row['renewed'] ?? 0),
            'loan_rules_id' => (int) ($row['loan_rules_id'] ?? 0),
            'actual' => dateValue($row['actual'] ?? null),
            'is_lent' => (int) ($row['is_lent'] ?? 0),
            'is_return' => (int) ($row['is_return'] ?? 0),
            'return_date' => dateValue($row['return_date'] ?? null),
            'input_date' => dateTimeValue($row['input_date'] ?? null),
            'last_update' => dateTimeValue($row['last_update'] ?? null),
            'uid' => nullableInt($row['uid'] ?? null),
        ]);
        recordMigrationMap($target, $source, 'loan', $oldLoanId, 'loan', $newLoanId);
        $summary['loan']++;
    }

    return $summary;
}

function migrateInlisSource(PDO $target, PDO $sourceDb, array $source, array &$generatedMaps): array
{
    $summary = [
        'biblio' => 0,
        'item' => 0,
        'member' => 0,
        'loan' => 0,
        'loanSkipped' => 0,
        'author' => 0,
        'publisher' => 0,
        'topic' => 0,
    ];

    $offset = (int) $source['offset'];
    $catalogAuthors = [];
    $catalogTopics = [];

    foreach ($sourceDb->query('SELECT * FROM catalogs ORDER BY ID') as $row) {
        $catalogId = (int) $row['ID'];
        $newBiblioId = $offset + $catalogId;
        $publisherId = null;
        $publisherName = cleanText($row['Publisher'] ?? null);
        if ($publisherName !== null && $publisherName !== '') {
            $publisherId = ensureGeneratedPublisher($target, $publisherName, $generatedMaps);
            recordMigrationMap($target, $source, 'catalogs.Publisher', $catalogId . ':' . $publisherName, 'mst_publisher', $publisherId);
            $summary['publisher']++;
        }

        $authorNames = splitNames((string) ($row['Author'] ?? ''));
        foreach ($authorNames as $authorName) {
            $authorId = ensureGeneratedAuthor($target, $authorName, $generatedMaps);
            $catalogAuthors[$catalogId][] = $authorId;
            recordMigrationMap($target, $source, 'catalogs.Author', $catalogId . ':' . $authorName, 'mst_author', $authorId);
        }

        $topicNames = splitNames((string) ($row['Subject'] ?? ''));
        foreach ($topicNames as $topicName) {
            $topicId = ensureGeneratedTopic($target, $topicName, $generatedMaps);
            $catalogTopics[$catalogId][] = $topicId;
            recordMigrationMap($target, $source, 'catalogs.Subject', $catalogId . ':' . $topicName, 'mst_topic', $topicId);
        }

        insertRow($target, 'biblio', [
            'biblio_id' => $newBiblioId,
            'gmd_id' => null,
            'title' => cleanText($row['Title'] ?? 'Tanpa Judul') ?: 'Tanpa Judul',
            'sor' => null,
            'edition' => limitText(cleanText($row['Edition'] ?? null), 50),
            'isbn_issn' => limitText(cleanText($row['ISBN'] ?? null), 32),
            'publisher_id' => $publisherId,
            'publish_year' => limitText(cleanText($row['PublishYear'] ?? null), 20),
            'collation' => limitText(cleanText($row['PhysicalDescription'] ?? null), 100),
            'series_title' => null,
            'call_number' => limitText(cleanText($row['CallNumber'] ?? null), 50),
            'language_id' => normalizeLanguage($row['Languages'] ?? null),
            'source' => $source['code'],
            'publish_place_id' => null,
            'classification' => limitText(cleanText($row['DeweyNo'] ?? null), 40),
            'notes' => cleanText($row['Note'] ?? null),
            'image' => limitText(cleanText($row['CoverURL'] ?? null), 100),
            'file_att' => null,
            'opac_hide' => ((int) ($row['IsOPAC'] ?? 1)) === 1 ? 0 : 1,
            'promoted' => 0,
            'labels' => cleanText($row['Subject'] ?? null),
            'frequency_id' => 0,
            'spec_detail_info' => cleanText($row['Publikasi'] ?? null),
            'content_type_id' => null,
            'media_type_id' => null,
            'carrier_type_id' => null,
            'input_date' => dateTimeValue($row['CreateDate'] ?? null),
            'last_update' => dateTimeValue($row['UpdateDate'] ?? null),
            'uid' => null,
        ]);
        recordMigrationMap($target, $source, 'catalogs', $catalogId, 'biblio', $newBiblioId);
        $summary['biblio']++;
    }

    foreach ($catalogAuthors as $catalogId => $authorIds) {
        foreach (array_values(array_unique($authorIds)) as $level => $authorId) {
            insertRow($target, 'biblio_author', [
                'biblio_id' => $offset + (int) $catalogId,
                'author_id' => $authorId,
                'level' => $level + 1,
            ], true);
        }
    }

    foreach ($catalogTopics as $catalogId => $topicIds) {
        foreach (array_values(array_unique($topicIds)) as $level => $topicId) {
            insertRow($target, 'biblio_topic', [
                'biblio_id' => $offset + (int) $catalogId,
                'topic_id' => $topicId,
                'level' => $level + 1,
            ], true);
        }
    }

    foreach ($sourceDb->query('SELECT * FROM collections ORDER BY ID') as $row) {
        $collectionId = (int) $row['ID'];
        $newItemId = $offset + $collectionId;
        insertRow($target, 'item', [
            'item_id' => $newItemId,
            'biblio_id' => nullableInt($row['Catalog_id'] ?? null) ? $offset + (int) $row['Catalog_id'] : null,
            'call_number' => limitText(cleanText($row['CallNumber'] ?? null), 50),
            'coll_type_id' => nullableInt($row['Category_id'] ?? null),
            'item_code' => makeItemCode($source['code'], (string) ($row['NomorBarcode'] ?? ''), $collectionId),
            'inventory_code' => limitText(cleanText($row['NoInduk'] ?? null), 200),
            'received_date' => dateValue($row['TanggalPengadaan'] ?? null),
            'supplier_id' => null,
            'order_no' => null,
            'location_id' => $source['code'],
            'order_date' => null,
            'item_status_id' => mapInlisItemStatus($row['Status_id'] ?? null),
            'site' => limitText('INLIS Location ' . (string) ($row['Location_id'] ?? ''), 50),
            'source' => nullableInt($row['Source_id'] ?? null) ?: 0,
            'invoice' => null,
            'price' => nullableInt($row['Price'] ?? null),
            'price_currency' => limitText(cleanText($row['Currency'] ?? null), 10),
            'invoice_date' => null,
            'input_date' => dateTimeValue($row['CreateDate'] ?? null, date('Y-m-d H:i:s')),
            'last_update' => dateTimeValue($row['UpdateDate'] ?? null),
            'uid' => null,
        ]);
        recordMigrationMap($target, $source, 'collections', $collectionId, 'item', $newItemId);
        $summary['item']++;
    }

    $memberMap = [];
    foreach ($sourceDb->query('SELECT * FROM members ORDER BY ID') as $row) {
        $oldMemberId = (string) ($row['MemberNo'] ?: $row['ID']);
        $newMemberId = makeMemberId($source['code'], $oldMemberId);
        $memberMap[(string) $row['ID']] = $newMemberId;

        insertRow($target, 'member', [
            'member_id' => $newMemberId,
            'member_name' => limitText(cleanText($row['Fullname'] ?? ''), 100, 'Tanpa Nama'),
            'gender' => mapInlisGender($row['Sex_id'] ?? null),
            'birth_date' => dateValue($row['DateOfBirth'] ?? null),
            'member_type_id' => 1,
            'member_address' => limitText(cleanText($row['Address'] ?? $row['AddressNow'] ?? null), 255),
            'member_mail_address' => limitText(cleanText($row['AddressNow'] ?? null), 255),
            'member_email' => limitText(cleanText($row['Email'] ?? null), 100),
            'postal_code' => null,
            'inst_name' => limitText(cleanText($row['InstitutionName'] ?? $source['name']), 100),
            'is_new' => 0,
            'member_image' => limitText(cleanText($row['PhotoUrl'] ?? null), 200),
            'pin' => null,
            'member_phone' => limitText(cleanText($row['Phone'] ?? $row['NoHp'] ?? null), 50),
            'member_fax' => null,
            'member_since_date' => dateValue($row['RegisterDate'] ?? null),
            'register_date' => dateValue($row['RegisterDate'] ?? null),
            'expire_date' => dateValue($row['EndDate'] ?? null, '2099-12-31'),
            'member_notes' => cleanText($row['KeteranganLain'] ?? null),
            'is_pending' => 0,
            'mpasswd' => null,
            'last_login' => null,
            'last_login_ip' => null,
            'input_date' => dateValue($row['CreateDate'] ?? null),
            'last_update' => dateValue($row['UpdateDate'] ?? null),
        ]);
        recordMigrationMap($target, $source, 'members', (string) $row['ID'], 'member', $newMemberId);
        $summary['member']++;
    }

    if (tableHasRows($sourceDb, 'collectionloanitems')) {
        foreach ($sourceDb->query('SELECT * FROM collectionloanitems ORDER BY ID') as $row) {
            $loanId = $offset + (int) $row['ID'];
            $collectionId = (int) $row['Collection_id'];
            $memberSourceId = (string) $row['member_id'];
            if (!isset($memberMap[$memberSourceId])) {
                $summary['loanSkipped']++;
                continue;
            }

            insertRow($target, 'loan', [
                'loan_id' => $loanId,
                'item_code' => makeItemCode($source['code'], '', $collectionId),
                'member_id' => $memberMap[$memberSourceId],
                'loan_date' => dateValue($row['LoanDate'] ?? null, date('Y-m-d')),
                'due_date' => dateValue($row['DueDate'] ?? null, date('Y-m-d')),
                'renewed' => 0,
                'loan_rules_id' => 0,
                'actual' => dateValue($row['ActualReturn'] ?? null),
                'is_lent' => empty($row['ActualReturn']) ? 1 : 0,
                'is_return' => empty($row['ActualReturn']) ? 0 : 1,
                'return_date' => dateValue($row['ActualReturn'] ?? null),
                'input_date' => dateTimeValue($row['CreateDate'] ?? null),
                'last_update' => dateTimeValue($row['UpdateDate'] ?? null),
                'uid' => null,
            ]);
            recordMigrationMap($target, $source, 'collectionloanitems', (string) $row['ID'], 'loan', $loanId);
            $summary['loan']++;
        }
    }

    $summary['author'] = count($generatedMaps['authors']);
    $summary['topic'] = count($generatedMaps['topics']);

    return $summary;
}

function syncLibraryTotals(PDO $target, array $sources): void
{
    $statement = $target->prepare(
        'UPDATE bdt_library
         SET total_koleksi = (
                 SELECT COUNT(DISTINCT biblio_id)
                 FROM item
                 WHERE location_id = bdt_library.slims_location_id
             ),
             total_anggota = ?,
             updated_at = NOW()
         WHERE slims_location_id = ?'
    );

    $memberCount = $target->prepare('SELECT COUNT(*) FROM member WHERE member_id LIKE ?');

    foreach ($sources as $source) {
        $memberCount->execute([$source['code'] . '-%']);
        $statement->execute([(int) $memberCount->fetchColumn(), $source['code']]);
    }
}

function buildVerification(PDO $target, array $sources): array
{
    $verification = [
        'total_biblio' => (int) $target->query('SELECT COUNT(*) FROM biblio WHERE biblio_id >= 100000')->fetchColumn(),
        'total_item' => (int) $target->query('SELECT COUNT(*) FROM item WHERE item_id >= 100000')->fetchColumn(),
        'total_member' => (int) $target->query("SELECT COUNT(*) FROM member WHERE member_id LIKE 'SBJ-%' OR member_id LIKE 'SD2-%' OR member_id LIKE 'S1T-%' OR member_id LIKE 'S2T-%'")->fetchColumn(),
        'total_loan' => (int) $target->query('SELECT COUNT(*) FROM loan WHERE loan_id >= 100000')->fetchColumn(),
        'locations' => [],
    ];

    $itemCount = $target->prepare('SELECT COUNT(*) FROM item WHERE location_id = ?');
    $titleCount = $target->prepare('SELECT COUNT(DISTINCT biblio_id) FROM item WHERE location_id = ?');
    $libraryCount = $target->prepare('SELECT total_koleksi, total_anggota, status FROM bdt_library WHERE slims_location_id = ? LIMIT 1');
    $mapCount = $target->prepare('SELECT COUNT(*) FROM bdt_migration_map WHERE source_code = ?');

    foreach ($sources as $source) {
        $itemCount->execute([$source['code']]);
        $titleCount->execute([$source['code']]);
        $libraryCount->execute([$source['code']]);
        $mapCount->execute([$source['code']]);
        $libraryRow = $libraryCount->fetch() ?: [];

        $verification['locations'][$source['code']] = [
            'name' => $source['name'],
            'items' => (int) $itemCount->fetchColumn(),
            'titles' => (int) $titleCount->fetchColumn(),
            'library_total_koleksi' => (int) ($libraryRow['total_koleksi'] ?? 0),
            'library_total_anggota' => (int) ($libraryRow['total_anggota'] ?? 0),
            'migration_map_rows' => (int) $mapCount->fetchColumn(),
            'status' => (string) ($libraryRow['status'] ?? ''),
        ];
    }

    return $verification;
}

function printSummary(array $summary, array $verification): void
{
    echo "Migrasi selesai.\n\n";
    foreach ($summary as $sourceKey => $counts) {
        echo strtoupper($sourceKey) . "\n";
        echo "- biblio: {$counts['biblio']}\n";
        echo "- item: {$counts['item']}\n";
        echo "- member: {$counts['member']}\n";
        echo "- loan: {$counts['loan']}\n";
        if (($counts['loanSkipped'] ?? 0) > 0) {
            echo "- loan skipped: {$counts['loanSkipped']}\n";
        }
    }

    echo "\nVerifikasi target\n";
    echo "- total biblio migrasi: {$verification['total_biblio']}\n";
    echo "- total item migrasi: {$verification['total_item']}\n";
    echo "- total member migrasi: {$verification['total_member']}\n";
    echo "- total loan migrasi: {$verification['total_loan']}\n";

    foreach ($verification['locations'] as $code => $row) {
        echo "- {$code} {$row['name']}: {$row['titles']} judul, {$row['items']} item, {$row['migration_map_rows']} map, status {$row['status']}\n";
    }
}

function recordMigrationMap(
    PDO $target,
    array $source,
    string $sourceTable,
    int|string $oldId,
    string $targetTable,
    int|string $newId
): void {
    if (!isset($source['library_id'])) {
        throw new RuntimeException("Entity perpustakaan {$source['code']} belum memiliki library_id.");
    }

    insertRow($target, 'bdt_migration_map', [
        'library_id' => (int) $source['library_id'],
        'source_code' => $source['code'],
        'source_database' => $source['db'],
        'source_type' => $source['type'],
        'source_table' => $sourceTable,
        'old_id' => (string) $oldId,
        'target_table' => $targetTable,
        'new_id' => (string) $newId,
        'created_at' => date('Y-m-d H:i:s'),
    ], true);
}

function ensureSlimsAuthor(PDO $target, array $data): int
{
    static $select = null;

    if ($select === null) {
        $select = $target->prepare(
            'SELECT author_id FROM mst_author WHERE author_name = ? AND authority_type = ? LIMIT 1'
        );
    }

    $select->execute([$data['author_name'], $data['authority_type']]);
    $existingId = $select->fetchColumn();
    if ($existingId !== false) {
        return (int) $existingId;
    }

    insertRow($target, 'mst_author', $data);
    return (int) $data['author_id'];
}

function ensureSlimsPublisher(PDO $target, array $data): int
{
    static $select = null;

    if ($select === null) {
        $select = $target->prepare('SELECT publisher_id FROM mst_publisher WHERE publisher_name = ? LIMIT 1');
    }

    $select->execute([$data['publisher_name']]);
    $existingId = $select->fetchColumn();
    if ($existingId !== false) {
        return (int) $existingId;
    }

    insertRow($target, 'mst_publisher', $data);
    return (int) $data['publisher_id'];
}

function ensureSlimsTopic(PDO $target, array $data): int
{
    static $select = null;

    if ($select === null) {
        $select = $target->prepare('SELECT topic_id FROM mst_topic WHERE topic = ? AND topic_type = ? LIMIT 1');
    }

    $select->execute([$data['topic'], $data['topic_type']]);
    $existingId = $select->fetchColumn();
    if ($existingId !== false) {
        return (int) $existingId;
    }

    insertRow($target, 'mst_topic', $data);
    return (int) $data['topic_id'];
}

function ensureGeneratedAuthor(PDO $target, string $name, array &$maps): int
{
    $normalized = normalizeKey($name);
    if (isset($maps['authors'][$normalized])) {
        return $maps['authors'][$normalized];
    }

    static $select = null;
    if ($select === null) {
        $select = $target->prepare('SELECT author_id FROM mst_author WHERE author_name = ? AND authority_type = "p" LIMIT 1');
    }

    $select->execute([limitText($name, 100, 'Tidak diketahui')]);
    $existingId = $select->fetchColumn();
    if ($existingId !== false) {
        $maps['authors'][$normalized] = (int) $existingId;
        return (int) $existingId;
    }

    $authorId = $maps['nextAuthorId']++;
    insertRow($target, 'mst_author', [
        'author_id' => $authorId,
        'author_name' => limitText($name, 100, 'Tidak diketahui'),
        'author_year' => null,
        'authority_type' => 'p',
        'auth_list' => null,
        'input_date' => date('Y-m-d'),
        'last_update' => date('Y-m-d'),
    ]);
    $maps['authors'][$normalized] = $authorId;
    return $authorId;
}

function ensureGeneratedPublisher(PDO $target, string $name, array &$maps): int
{
    $normalized = normalizeKey($name);
    if (isset($maps['publishers'][$normalized])) {
        return $maps['publishers'][$normalized];
    }

    static $select = null;
    if ($select === null) {
        $select = $target->prepare('SELECT publisher_id FROM mst_publisher WHERE publisher_name = ? LIMIT 1');
    }

    $select->execute([limitText($name, 100, 'Tidak diketahui')]);
    $existingId = $select->fetchColumn();
    if ($existingId !== false) {
        $maps['publishers'][$normalized] = (int) $existingId;
        return (int) $existingId;
    }

    $publisherId = $maps['nextPublisherId']++;
    insertRow($target, 'mst_publisher', [
        'publisher_id' => $publisherId,
        'publisher_name' => limitText($name, 100, 'Tidak diketahui'),
        'input_date' => date('Y-m-d'),
        'last_update' => date('Y-m-d'),
    ]);
    $maps['publishers'][$normalized] = $publisherId;
    return $publisherId;
}

function ensureGeneratedTopic(PDO $target, string $name, array &$maps): int
{
    $normalized = normalizeKey($name);
    if (isset($maps['topics'][$normalized])) {
        return $maps['topics'][$normalized];
    }

    static $select = null;
    if ($select === null) {
        $select = $target->prepare('SELECT topic_id FROM mst_topic WHERE topic = ? AND topic_type = "t" LIMIT 1');
    }

    $select->execute([limitText($name, 50, 'Tidak diketahui')]);
    $existingId = $select->fetchColumn();
    if ($existingId !== false) {
        $maps['topics'][$normalized] = (int) $existingId;
        return (int) $existingId;
    }

    $topicId = $maps['nextTopicId']++;
    insertRow($target, 'mst_topic', [
        'topic_id' => $topicId,
        'topic' => limitText($name, 50, 'Tidak diketahui'),
        'topic_type' => 't',
        'auth_list' => null,
        'classification' => '',
        'input_date' => date('Y-m-d'),
        'last_update' => date('Y-m-d'),
    ]);
    $maps['topics'][$normalized] = $topicId;
    return $topicId;
}

function insertRow(PDO $pdo, string $table, array $data, bool $ignore = false): void
{
    static $statements = [];

    $columns = array_keys($data);
    $key = $table . ':' . ($ignore ? 'ignore:' : 'insert:') . implode(',', $columns);
    if (!isset($statements[$key])) {
        $columnSql = implode(', ', array_map(static fn(string $column): string => "`{$column}`", $columns));
        $placeholderSql = implode(', ', array_fill(0, count($columns), '?'));
        $mode = $ignore ? 'INSERT IGNORE' : 'INSERT';
        $statements[$key] = $pdo->prepare("{$mode} INTO `{$table}` ({$columnSql}) VALUES ({$placeholderSql})");
    }

    $statements[$key]->execute(array_values($data));
}

function tableHasRows(PDO $pdo, string $table): bool
{
    return (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn() > 0;
}

function tableExists(PDO $pdo, string $table): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = ?'
    );
    $statement->execute([$table]);

    return (int) $statement->fetchColumn() > 0;
}

function makeItemCode(string $sourceCode, string $rawCode, int $fallbackId): string
{
    $clean = preg_replace('/[^A-Za-z0-9]/', '', $rawCode);
    if ($clean === null || $clean === '') {
        return limitText($sourceCode . '-' . $fallbackId, 20, $sourceCode . '-' . substr(md5((string) $fallbackId), 0, 12)) ?? $sourceCode;
    }

    $candidate = $sourceCode . '-' . $clean . '-' . $fallbackId;
    if (mb_strlen($candidate) <= 20) {
        return $candidate;
    }

    return limitText($sourceCode . '-' . $fallbackId, 20, $sourceCode . '-' . substr(md5($clean . $fallbackId), 0, 12)) ?? $sourceCode;
}

function makeMemberId(string $sourceCode, string $rawMemberId): string
{
    $clean = preg_replace('/[^A-Za-z0-9]/', '', $rawMemberId);
    if ($clean === null || $clean === '') {
        $clean = 'UNKNOWN';
    }

    return limitText($sourceCode . '-' . $clean, 20, $sourceCode . '-' . substr(md5($rawMemberId), 0, 12));
}

function splitNames(string $value): array
{
    $value = cleanText($value) ?? '';
    if ($value === '') {
        return [];
    }

    $parts = preg_split('/\s*(?:;|\/|\band\b|\bdan\b)\s*/i', $value) ?: [];
    $names = [];
    foreach ($parts as $part) {
        $part = trim($part, " \t\n\r\0\x0B,.");
        if ($part !== '') {
            $names[] = $part;
        }
    }

    return array_slice(array_values(array_unique($names)), 0, 6);
}

function normalizeKey(string $value): string
{
    return mb_strtolower(trim(preg_replace('/\s+/', ' ', $value) ?? $value));
}

function cleanText(mixed $value): ?string
{
    if ($value === null) {
        return null;
    }

    $text = trim((string) $value);
    if ($text === '' || strtoupper($text) === 'NULL') {
        return null;
    }

    return preg_replace('/\s+/', ' ', $text) ?: null;
}

function limitText(?string $value, int $length, ?string $fallback = null): ?string
{
    $value = cleanText($value) ?? $fallback;
    if ($value === null) {
        return null;
    }

    return mb_substr($value, 0, $length);
}

function nullableInt(mixed $value): ?int
{
    if ($value === null || $value === '' || $value === '0' || $value === 0) {
        return null;
    }

    return (int) $value;
}

function dateValue(mixed $value, ?string $fallback = null): ?string
{
    $text = cleanText($value);
    if ($text === null || str_starts_with($text, '0000-00-00')) {
        return $fallback;
    }

    return substr($text, 0, 10);
}

function dateTimeValue(mixed $value, ?string $fallback = null): ?string
{
    $text = cleanText($value);
    if ($text === null || str_starts_with($text, '0000-00-00')) {
        return $fallback;
    }

    if (strlen($text) === 10) {
        return $text . ' 00:00:00';
    }

    return substr($text, 0, 19);
}

function validAuthorityType(mixed $value): string
{
    $value = cleanText($value) ?? 'p';
    return in_array($value, ['p', 'o', 'c'], true) ? $value : 'p';
}

function validTopicType(mixed $value): string
{
    $value = cleanText($value) ?? 't';
    return in_array($value, ['t', 'g', 'n', 'tm', 'gr', 'oc'], true) ? $value : 't';
}

function mapSlimsItemStatus(mixed $value): string
{
    $status = strtoupper((string) cleanText($value));
    if ($status === '' || $status === '0') {
        return 'AVL';
    }

    if (in_array($status, ['MIS', 'R', 'NL', 'WD', 'LO', 'AVL'], true)) {
        return $status;
    }

    return 'AVL';
}

function mapInlisItemStatus(mixed $value): string
{
    $status = (int) $value;
    return match ($status) {
        3, 4 => 'R',
        5 => 'LO',
        8 => 'MIS',
        9 => 'NL',
        10 => 'WD',
        default => 'AVL',
    };
}

function mapInlisGender(mixed $value): int
{
    $gender = (int) $value;
    return $gender > 0 ? $gender : 0;
}

function normalizeLanguage(mixed $value): string
{
    $language = strtolower((string) cleanText($value));
    if ($language === '' || $language === '###') {
        return 'id';
    }

    return limitText($language, 5, 'id') ?? 'id';
}
=======

declare(strict_types=1);

/**
 * SLiMS school data migrator
 *
 * Usage:
 *   php custom/scripts/migrate_data.php
 *   php custom/scripts/migrate_data.php --dry-run
 *   php custom/scripts/migrate_data.php --sources=temp_sdbanjarsari,temp_sdn2teras,temp_smpn1teras,temp_smpn2teras
 *
 * The script copies data from staging databases into the main bacaditeras
 * database while remapping IDs so school datasets do not collide.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script must be run from the command line.\n");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/custom/config/database.php';

$defaultSourceConfigs = [
    [
        'database' => 'temp_sdbanjarsari',
        'school_name' => 'SD Negeri 1 Teras',
        'location_id' => 'SD1',
        'id_offset' => 100000,
        'key_prefix' => 'S1T',
    ],
    [
        'database' => 'temp_sdn2teras',
        'school_name' => 'SDN 2 Teras',
        'location_id' => 'SD2',
        'id_offset' => 200000,
        'key_prefix' => 'S2T',
    ],
    [
        'database' => 'temp_smpn1teras',
        'school_name' => 'SMP Negeri 1 Teras',
        'location_id' => 'SP1',
        'id_offset' => 300000,
        'key_prefix' => 'SP1',
    ],
    [
        'database' => 'temp_smpn2teras',
        'school_name' => 'SMP Negeri 2 Teras',
        'location_id' => 'SP2',
        'id_offset' => 400000,
        'key_prefix' => 'SP2',
    ],
];

$options = getopt('', ['sources::', 'dry-run', 'help']);

if (isset($options['help'])) {
    fwrite(STDOUT, "Usage: php custom/scripts/migrate_data.php [--dry-run] [--sources=db1,db2,db3,db4]\n");
    exit(0);
}

$dryRun = array_key_exists('dry-run', $options);
$sourceConfigs = buildSourceConfigs($defaultSourceConfigs, $options['sources'] ?? null);

$targetConnection = connectDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

$currentDate = date('Y-m-d');
$currentDateTime = date('Y-m-d H:i:s');

seedLibraryRegistry($targetConnection, $currentDate, $currentDateTime, $dryRun);

$summary = [];

foreach ($sourceConfigs as $sourceConfig) {
    $sourceConnection = connectDatabase(DB_HOST, DB_USER, DB_PASS, $sourceConfig['database'], DB_PORT);
    $summary[] = migrateSourceDatabase(
        $sourceConnection,
        $targetConnection,
        $sourceConfig,
        $currentDate,
        $currentDateTime,
        $dryRun
    );
    $sourceConnection->close();
}

$targetConnection->close();

foreach ($summary as $sourceSummary) {
    fwrite(STDOUT, $sourceSummary . "\n");
}

fwrite(STDOUT, $dryRun ? "Dry run complete. No changes were written.\n" : "Migration complete.\n");

/**
 * @param array<int, array<string, mixed>> $defaultSourceConfigs
 * @return array<int, array<string, mixed>>
 */
function buildSourceConfigs(array $defaultSourceConfigs, ?string $sourceList): array
{
    if ($sourceList === null || trim($sourceList) === '') {
        return $defaultSourceConfigs;
    }

    $requestedSources = array_values(array_filter(array_map('trim', explode(',', $sourceList))));

    if (count($requestedSources) === 0) {
        return $defaultSourceConfigs;
    }

    $matchedConfigs = [];

    foreach ($requestedSources as $requestedSource) {
        foreach ($defaultSourceConfigs as $config) {
            if ($config['database'] === $requestedSource) {
                $matchedConfigs[] = $config;
                continue 2;
            }
        }

        throw new RuntimeException('Unknown source database: ' . $requestedSource);
    }

    return $matchedConfigs;
}

function connectDatabase(string $host, string $user, string $password, string $database, int $port): mysqli
{
    $connection = new mysqli($host, $user, $password, $database, $port);
    $connection->set_charset(DB_CHARSET);

    return $connection;
}

function seedLibraryRegistry(mysqli $targetConnection, string $currentDate, string $currentDateTime, bool $dryRun): void
{
    if ($dryRun) {
        return;
    }

    $locations = [
        ['DTR', 'Perpustakaan Desa Teras'],
        ['SD1', 'SD Negeri 1 Teras'],
        ['SD2', 'SD Negeri 2 Teras'],
        ['SP1', 'SMP Negeri 1 Teras'],
        ['SP2', 'SMP Negeri 2 Teras'],
    ];

    $locationStatement = $targetConnection->prepare(
        'INSERT IGNORE INTO mst_location (location_id, location_name, input_date, last_update) VALUES (?, ?, ?, ?)'
    );

    foreach ($locations as [$locationId, $locationName]) {
        $locationStatement->bind_param('ssss', $locationId, $locationName, $currentDate, $currentDate);
        $locationStatement->execute();
    }

    $locationStatement->close();

    $targetConnection->query(
        "UPDATE bdt_library SET status = 'nonaktif' WHERE slug IN ('perpustakaan-utama', 'taman-baca-komunitas', 'perpustakaan-digital')"
    );

    $libraryRows = [
        [
            'DTR',
            'perpustakaan-desa-teras',
            'Perpustakaan Desa Teras',
            'Pusat Literasi Desa Teras',
            'Unggulan',
            'aktif',
            'Desa Teras, Boyolali',
            'Desa Teras',
            '(0276) 000-000',
            'perpustakaan@teras.desa.id',
            '6281200001000',
            'https://maps.google.com/?q=Desa+Teras+Boyolali',
            '/custom/assets/images/library-teras-utama.jpg',
            '/custom/assets/images/library-teras-utama.jpg',
            0,
            0,
            'Perpustakaan Desa Teras menjadi pusat literasi utama untuk warga desa, dengan koleksi bacaan umum, layanan informasi, dan ruang kegiatan komunitas.',
            'Perpustakaan ini dikembangkan sebagai wajah utama literasi desa dan penghubung antar sekolah serta komunitas baca di Teras.',
            'Menjadi pusat literasi desa yang ramah, inklusif, dan mudah diakses oleh seluruh warga.',
            'Menyediakan ruang baca, layanan informasi, dan kegiatan literasi yang mendukung budaya belajar sepanjang hayat.',
            1,
        ],
        [
            'SD1',
            'sd-negeri-1-teras',
            'SD Negeri 1 Teras',
            'Perpustakaan sekolah dasar',
            'Sekolah',
            'aktif',
            'SD Negeri 1 Teras, Boyolali',
            'Teras',
            '(0276) 000-001',
            'sd1@teras.desa.id',
            '6281200001001',
            'https://maps.google.com/?q=SD+Negeri+1+Teras',
            '/custom/assets/images/library-1.png',
            '/custom/assets/images/library-1.png',
            0,
            0,
            'Perpustakaan SD Negeri 1 Teras mendukung literasi dasar dan koleksi penunjang pembelajaran bagi siswa sekolah dasar.',
            'Disiapkan untuk mendukung kegiatan literasi, tugas sekolah, dan pembiasaan membaca di lingkungan SD Negeri 1 Teras.',
            'Mendorong siswa gemar membaca dan terbiasa belajar mandiri sejak dini.',
            'Menyediakan koleksi yang sesuai usia, ruang baca nyaman, dan dukungan kegiatan literasi sekolah.',
            2,
        ],
        [
            'SD2',
            'sd-negeri-2-teras',
            'SD Negeri 2 Teras',
            'Perpustakaan sekolah dasar',
            'Sekolah',
            'aktif',
            'SD Negeri 2 Teras, Boyolali',
            'Teras',
            '(0276) 000-002',
            'sd2@teras.desa.id',
            '6281200001002',
            'https://maps.google.com/?q=SD+Negeri+2+Teras',
            '/custom/assets/images/library-2.png',
            '/custom/assets/images/library-2.png',
            0,
            0,
            'Perpustakaan SD Negeri 2 Teras menjadi titik dukung literasi dasar bagi siswa dengan fokus pada bacaan anak dan kegiatan belajar.',
            'Perpustakaan sekolah ini dirancang untuk memperkaya bacaan siswa, guru, dan orang tua yang terlibat dalam pendidikan dasar.',
            'Menumbuhkan kebiasaan membaca yang menyenangkan di lingkungan sekolah.',
            'Menyediakan akses buku, layanan peminjaman, dan kegiatan literasi yang terarah.',
            3,
        ],
        [
            'SP1',
            'smp-negeri-1-teras',
            'SMP Negeri 1 Teras',
            'Perpustakaan sekolah menengah pertama',
            'Sekolah',
            'aktif',
            'SMP Negeri 1 Teras, Boyolali',
            'Teras',
            '(0276) 000-003',
            'smp1@teras.desa.id',
            '6281200001003',
            'https://maps.google.com/?q=SMP+Negeri+1+Teras',
            '/custom/assets/images/library-3.png',
            '/custom/assets/images/library-3.png',
            0,
            0,
            'Perpustakaan SMP Negeri 1 Teras mendukung pembelajaran tingkat menengah pertama dengan koleksi referensi dan bacaan remaja.',
            'Menjadi ruang belajar yang membantu siswa menemukan referensi, inspirasi, dan kebiasaan riset sederhana.',
            'Menjadi perpustakaan sekolah yang responsif terhadap kebutuhan belajar abad 21.',
            'Menyediakan koleksi relevan, ruang baca produktif, dan dukungan literasi digital.',
            4,
        ],
        [
            'SP2',
            'smp-negeri-2-teras',
            'SMP Negeri 2 Teras',
            'Perpustakaan sekolah menengah pertama',
            'Sekolah',
            'aktif',
            'SMP Negeri 2 Teras, Boyolali',
            'Teras',
            '(0276) 000-004',
            'smp2@teras.desa.id',
            '6281200001004',
            'https://maps.google.com/?q=SMP+Negeri+2+Teras',
            '/custom/assets/images/hero-library.png',
            '/custom/assets/images/hero-library.png',
            0,
            0,
            'Perpustakaan SMP Negeri 2 Teras menyediakan akses koleksi pembelajaran, referensi, dan ruang literasi yang mendukung aktivitas sekolah.',
            'Diarahkan untuk memperluas wawasan siswa melalui buku, media baca, dan kegiatan literasi yang konsisten.',
            'Menjadi pusat literasi sekolah yang adaptif dan inklusif.',
            'Menyediakan layanan perpustakaan yang mendukung pembelajaran dan budaya baca siswa.',
            5,
        ],
    ];

    foreach ($libraryRows as $libraryRow) {
        $row = [
            'slims_location_id' => $libraryRow[0],
            'slug' => $libraryRow[1],
            'name' => $libraryRow[2],
            'tagline' => $libraryRow[3],
            'badge' => $libraryRow[4],
            'status' => $libraryRow[5],
            'address' => $libraryRow[6],
            'village' => $libraryRow[7],
            'phone' => $libraryRow[8],
            'email' => $libraryRow[9],
            'whatsapp' => $libraryRow[10],
            'google_maps_url' => $libraryRow[11],
            'cover_image' => $libraryRow[12],
            'thumbnail_image' => $libraryRow[13],
            'total_koleksi' => $libraryRow[14],
            'total_anggota' => $libraryRow[15],
            'description' => $libraryRow[16],
            'sejarah' => $libraryRow[17],
            'visi' => $libraryRow[18],
            'misi' => $libraryRow[19],
            'sort_order' => $libraryRow[20],
        ];
        insertRow($targetConnection, 'bdt_library', array_keys($row), $row);
    }
}

/**
 * @return string
 */
function migrateSourceDatabase(
    mysqli $sourceConnection,
    mysqli $targetConnection,
    array $sourceConfig,
    string $currentDate,
    string $currentDateTime,
    bool $dryRun
): string {
    $schoolName = (string) $sourceConfig['school_name'];
    $locationId = (string) $sourceConfig['location_id'];
    $idOffset = (int) $sourceConfig['id_offset'];
    $keyPrefix = (string) $sourceConfig['key_prefix'];

    $sorMap = loadSorMap($sourceConnection);
    $authorMap = migrateIntegerKeyTable($sourceConnection, $targetConnection, 'mst_author', 'author_id', $idOffset, $dryRun);
    $publisherMap = migrateIntegerKeyTable($sourceConnection, $targetConnection, 'mst_publisher', 'publisher_id', $idOffset, $dryRun);
    $topicMap = migrateIntegerKeyTable($sourceConnection, $targetConnection, 'mst_topic', 'topic_id', $idOffset, $dryRun);

    ensureLocationRow($targetConnection, $locationId, $schoolName, $currentDate, $currentDateTime, $dryRun);

    $biblioMap = migrateBiblioTable(
        $sourceConnection,
        $targetConnection,
        $idOffset,
        $sorMap,
        $authorMap,
        $publisherMap,
        $dryRun
    );

    migrateRelationTable($sourceConnection, $targetConnection, 'biblio_author', $biblioMap, $authorMap, $idOffset, $dryRun);
    migrateRelationTable($sourceConnection, $targetConnection, 'biblio_topic', $biblioMap, $topicMap, $idOffset, $dryRun);

    $itemMap = migrateItemTable(
        $sourceConnection,
        $targetConnection,
        $biblioMap,
        $locationId,
        $keyPrefix,
        $idOffset,
        $dryRun
    );

    migrateCustomIdTable($sourceConnection, $targetConnection, 'biblio_custom', 'biblio_id', $biblioMap, $idOffset, $dryRun);
    migrateCustomIdTable($sourceConnection, $targetConnection, 'item_custom', 'item_id', $itemMap, $idOffset, $dryRun);
    migrateMemberTable($sourceConnection, $targetConnection, $keyPrefix, $dryRun);
    migrateLoanTable($sourceConnection, $targetConnection, $keyPrefix, $itemMap, $dryRun);

    return sprintf(
        '%s (%s): books=%d, authors=%d, publishers=%d, topics=%d, items=%d',
        $schoolName,
        (string) $sourceConfig['database'],
        count($biblioMap),
        count($authorMap),
        count($publisherMap),
        count($topicMap),
        count($itemMap)
    );
}

/**
 * @return array<int, string>
 */
function loadSorMap(mysqli $sourceConnection): array
{
    $map = [];

    if (!tableExists($sourceConnection, 'mst_sor')) {
        return $map;
    }

    $result = $sourceConnection->query('SELECT sor_id, sor FROM mst_sor');
    while ($row = $result->fetch_assoc()) {
        $map[(int) $row['sor_id']] = $row['sor'];
    }

    $result->free();

    return $map;
}

/**
 * @return array<int, int>
 */
function migrateIntegerKeyTable(
    mysqli $sourceConnection,
    mysqli $targetConnection,
    string $tableName,
    string $primaryKey,
    int $idOffset,
    bool $dryRun
): array {
    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return [];
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));

    $rows = fetchAllRows($sourceConnection, sprintf('SELECT * FROM %s ORDER BY %s', backtick($tableName), backtick($primaryKey)));
    $map = [];

    foreach ($rows as $row) {
        $oldId = (int) $row[$primaryKey];
        $newId = $oldId + $idOffset;
        $row[$primaryKey] = $newId;
        $map[$oldId] = $newId;

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }

    return $map;
}

/**
 * @return array<int, int>
 */
function migrateBiblioTable(
    mysqli $sourceConnection,
    mysqli $targetConnection,
    int $idOffset,
    array $sorMap,
    array $authorMap,
    array $publisherMap,
    bool $dryRun
): array {
    $tableName = 'biblio';

    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return [];
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));
    if (in_array('sor', $targetColumns, true) && !in_array('sor', $columns, true)) {
        $columns[] = 'sor';
    }
    $rows = fetchAllRows($sourceConnection, 'SELECT * FROM ' . backtick($tableName) . ' ORDER BY ' . backtick('biblio_id'));
    $map = [];

    foreach ($rows as $row) {
        $oldId = (int) $row['biblio_id'];
        $newId = $oldId + $idOffset;
        $map[$oldId] = $newId;
        $row['biblio_id'] = $newId;

        if (array_key_exists('sor', $targetColumns)) {
            $row['sor'] = resolveSorValue($row, $sorMap);
        }

        if (array_key_exists('publisher_id', $row) && isset($publisherMap[(int) $row['publisher_id']])) {
            $row['publisher_id'] = $publisherMap[(int) $row['publisher_id']];
        }

        if (array_key_exists('sor_id', $row)) {
            unset($row['sor_id']);
        }

        if (array_key_exists('author_id', $row) && isset($authorMap[(int) $row['author_id']])) {
            $row['author_id'] = $authorMap[(int) $row['author_id']];
        }

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }

    return $map;
}

function resolveSorValue(array $row, array $sorMap): ?string
{
    if (array_key_exists('sor', $row) && $row['sor'] !== null && $row['sor'] !== '') {
        return (string) $row['sor'];
    }

    if (array_key_exists('sor_id', $row) && $row['sor_id'] !== null && isset($sorMap[(int) $row['sor_id']])) {
        return (string) $sorMap[(int) $row['sor_id']];
    }

    return null;
}

/**
 * @param array<int, int> $parentMap
 * @param array<int, int> $lookupMap
 */
function migrateRelationTable(
    mysqli $sourceConnection,
    mysqli $targetConnection,
    string $tableName,
    array $parentMap,
    array $lookupMap,
    int $idOffset,
    bool $dryRun
): void {
    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return;
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));
    $rows = fetchAllRows($sourceConnection, 'SELECT * FROM ' . backtick($tableName));

    foreach ($rows as $row) {
        if (isset($row['biblio_id'])) {
            $row['biblio_id'] = isset($parentMap[(int) $row['biblio_id']]) ? $parentMap[(int) $row['biblio_id']] : ((int) $row['biblio_id'] + $idOffset);
        }

        if (isset($row['rel_biblio_id'])) {
            $row['rel_biblio_id'] = isset($parentMap[(int) $row['rel_biblio_id']]) ? $parentMap[(int) $row['rel_biblio_id']] : ((int) $row['rel_biblio_id'] + $idOffset);
        }

        if (isset($row['author_id'])) {
            $oldAuthorId = (int) $row['author_id'];
            $row['author_id'] = $lookupMap[$oldAuthorId] ?? ($oldAuthorId + $idOffset);
        }

        if (isset($row['topic_id'])) {
            $oldTopicId = (int) $row['topic_id'];
            $row['topic_id'] = $lookupMap[$oldTopicId] ?? ($oldTopicId + $idOffset);
        }

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }
}

/**
 * @return array<int, int>
 */
function migrateItemTable(
    mysqli $sourceConnection,
    mysqli $targetConnection,
    array $biblioMap,
    string $locationId,
    string $keyPrefix,
    int $idOffset,
    bool $dryRun
): array {
    $tableName = 'item';

    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return [];
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));
    $rows = fetchAllRows($sourceConnection, 'SELECT * FROM ' . backtick($tableName) . ' ORDER BY ' . backtick('item_id'));
    $map = [];

    foreach ($rows as $row) {
        $oldId = (int) $row['item_id'];
        $newId = $oldId + $idOffset;
        $map[$oldId] = $newId;
        $row['item_id'] = $newId;

        if (isset($row['biblio_id'])) {
            $oldBiblioId = (int) $row['biblio_id'];
            $row['biblio_id'] = $biblioMap[$oldBiblioId] ?? ($oldBiblioId + $idOffset);
        }

        if (array_key_exists('location_id', $row)) {
            $row['location_id'] = $locationId;
        }

        if (array_key_exists('item_code', $row)) {
            $row['item_code'] = remapStringIdentifier($keyPrefix, (string) ($row['item_code'] ?? ''), 20, (string) $row['item_id']);
        }

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }

    return $map;
}

/**
 * @param array<int, int> $idMap
 */
function migrateCustomIdTable(
    mysqli $sourceConnection,
    mysqli $targetConnection,
    string $tableName,
    string $primaryKey,
    array $idMap,
    int $idOffset,
    bool $dryRun
): void {
    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return;
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));
    $rows = fetchAllRows($sourceConnection, 'SELECT * FROM ' . backtick($tableName));

    foreach ($rows as $row) {
        $oldId = (int) $row[$primaryKey];
        $row[$primaryKey] = $idMap[$oldId] ?? ($oldId + $idOffset);

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }
}

function migrateMemberTable(mysqli $sourceConnection, mysqli $targetConnection, string $keyPrefix, bool $dryRun): void
{
    $tableName = 'member';

    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return;
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));
    $rows = fetchAllRows($sourceConnection, 'SELECT * FROM ' . backtick($tableName));

    foreach ($rows as $row) {
        if (array_key_exists('member_id', $row)) {
            $row['member_id'] = remapStringIdentifier($keyPrefix, (string) $row['member_id'], 20, (string) $row['member_id']);
        }

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }
}

function migrateLoanTable(mysqli $sourceConnection, mysqli $targetConnection, string $keyPrefix, array $itemMap, bool $dryRun): void
{
    $tableName = 'loan';

    if (!tableExists($sourceConnection, $tableName) || !tableExists($targetConnection, $tableName)) {
        return;
    }

    $sourceColumns = getTableColumns($sourceConnection, $tableName);
    $targetColumns = getTableColumns($targetConnection, $tableName);
    $columns = array_values(array_intersect($targetColumns, $sourceColumns));
    $rows = fetchAllRows($sourceConnection, 'SELECT * FROM ' . backtick($tableName) . ' ORDER BY ' . backtick('loan_id'));

    foreach ($rows as $row) {
        if (array_key_exists('loan_id', $row)) {
            $row['loan_id'] = (int) $row['loan_id'] + 500000;
        }

        if (array_key_exists('member_id', $row) && $row['member_id'] !== null && $row['member_id'] !== '') {
            $row['member_id'] = remapStringIdentifier($keyPrefix, (string) $row['member_id'], 20, (string) $row['member_id']);
        }

        if (array_key_exists('item_code', $row) && $row['item_code'] !== null && $row['item_code'] !== '') {
            $oldCode = (string) $row['item_code'];
            $row['item_code'] = remapStringIdentifier($keyPrefix, $oldCode, 20, $oldCode);
        }

        if ($dryRun) {
            continue;
        }

        insertRow($targetConnection, $tableName, $columns, $row);
    }
}

function ensureLocationRow(mysqli $targetConnection, string $locationId, string $locationName, string $currentDate, string $currentDateTime, bool $dryRun): void
{
    if (!tableExists($targetConnection, 'mst_location')) {
        return;
    }

    $sql = 'INSERT IGNORE INTO mst_location (location_id, location_name, input_date, last_update) VALUES (?, ?, ?, ?)';

    if ($dryRun) {
        return;
    }

    $statement = $targetConnection->prepare($sql);
    $statement->bind_param('ssss', $locationId, $locationName, $currentDate, $currentDate);
    $statement->execute();
    $statement->close();
}

/**
 * @return array<int, array<string, mixed>>
 */
function fetchAllRows(mysqli $connection, string $sql): array
{
    $result = $connection->query($sql);
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $result->free();

    return $rows;
}

/**
 * @return array<int, string>
 */
function getTableColumns(mysqli $connection, string $tableName): array
{
    $result = $connection->query('SHOW COLUMNS FROM ' . backtick($tableName));
    $columns = [];

    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    $result->free();

    return $columns;
}

function tableExists(mysqli $connection, string $tableName): bool
{
    $table = $connection->real_escape_string($tableName);
    $result = $connection->query("SHOW TABLES LIKE '{$table}'");
    $exists = $result->num_rows > 0;
    $result->free();

    return $exists;
}

/**
 * @param array<int, string> $columns
 * @param array<string, mixed> $row
 */
function insertRow(mysqli $connection, string $tableName, array $columns, array $row): void
{
    $insertColumns = [];
    $values = [];

    foreach ($columns as $column) {
        if (!array_key_exists($column, $row)) {
            continue;
        }

        $insertColumns[] = backtick($column);
        $values[] = normalizeDbValue($row[$column]);
    }

    if (count($insertColumns) === 0) {
        return;
    }

    $placeholders = implode(', ', array_fill(0, count($insertColumns), '?'));
    $sql = sprintf(
        'INSERT IGNORE INTO %s (%s) VALUES (%s)',
        backtick($tableName),
        implode(', ', $insertColumns),
        $placeholders
    );

    $statement = $connection->prepare($sql);
    $types = str_repeat('s', count($values));
    $statement->bind_param($types, ...$values);
    $statement->execute();
    $statement->close();
}

/**
 * @return mixed
 */
function normalizeDbValue(mixed $value): mixed
{
    if (is_bool($value)) {
        return $value ? 1 : 0;
    }

    if (is_array($value) || is_object($value)) {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    return $value;
}

function remapStringIdentifier(string $prefix, string $originalValue, int $maxLength, string $fallbackSeed): string
{
    $originalValue = trim($originalValue);
    $base = $originalValue !== '' ? $originalValue : $fallbackSeed;
    $candidate = $prefix . '-' . $base;

    if (strlen($candidate) <= $maxLength) {
        return $candidate;
    }

    $hashLength = max(4, $maxLength - strlen($prefix) - 1);
    $hash = substr(hash('sha1', $base), 0, $hashLength);
    $candidate = $prefix . '-' . $hash;

    return substr($candidate, 0, $maxLength);
}

function backtick(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}
>>>>>>> 67f9548d3f70e15d7bcac48ac6109dde69b020cc
