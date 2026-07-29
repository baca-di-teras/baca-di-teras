<?php
/**
 * Backup Service
 */

require_once __DIR__ . '/../helpers/database.php';

class BackupService
{
    public function downloadBackup(): void
    {
        $dbName = DB_NAME;
        $dbUser = DB_USER;
        $dbPass = DB_PASS;
        $dbHost = DB_HOST;
        
        $filename = "backup_bacaditeras_" . date("Y-m-d_H-i-s") . ".sql";
        
        // Coba gunakan mysqldump (XAMPP path)
        $mysqldumpPath = 'c:\xampp\mysql\bin\mysqldump.exe';
        if (!file_exists($mysqldumpPath)) {
            $mysqldumpPath = 'mysqldump'; // Fallback ke env PATH
        }
        
        $passString = empty($dbPass) ? "" : "-p{$dbPass}";
        $command = "{$mysqldumpPath} -h {$dbHost} -u {$dbUser} {$passString} {$dbName}";
        
        // Execute and capture output
        ob_start();
        passthru($command, $returnVar);
        $sqlOutput = ob_get_clean();
        
        if ($returnVar !== 0 || empty($sqlOutput)) {
            // Fallback native PHP dump jika mysqldump gagal
            $sqlOutput = $this->nativePhpDump();
        }
        
        // Set headers for download
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sqlOutput));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $sqlOutput;
        exit;
    }

    private function nativePhpDump(): string
    {
        $db = Database::getInstance()->getConnection();
        $output = "-- Backup Database Baca Di Teras\n";
        $output .= "-- Waktu: " . date("Y-m-d H:i:s") . "\n\n";
        
        $tables = [];
        $res = $db->query("SHOW TABLES");
        while ($row = $res->fetch_array()) {
            $tables[] = $row[0];
        }
        
        foreach ($tables as $table) {
            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Struktur tabel $table\n";
            $output .= "-- --------------------------------------------------------\n\n";
            
            $res = $db->query("SHOW CREATE TABLE $table");
            $row = $res->fetch_row();
            $output .= $row[1] . ";\n\n";
            
            $output .= "-- Dumping data untuk tabel $table\n\n";
            $res = $db->query("SELECT * FROM $table");
            if ($res->num_rows > 0) {
                $output .= "INSERT INTO `$table` VALUES \n";
                $rowsData = [];
                while ($row = $res->fetch_assoc()) {
                    $vals = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $vals[] = "NULL";
                        } else {
                            $vals[] = "'" . $db->real_escape_string($val) . "'";
                        }
                    }
                    $rowsData[] = "(" . implode(", ", $vals) . ")";
                }
                $output .= implode(",\n", $rowsData) . ";\n\n";
            }
        }
        return $output;
    }
}
