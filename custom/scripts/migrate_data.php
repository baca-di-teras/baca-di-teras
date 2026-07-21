<?php

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
        insertRow($targetConnection, 'bdt_library', [
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
        ]);
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