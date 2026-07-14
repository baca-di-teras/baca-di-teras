<?php
/**
 * Database Configuration
 *
 * File    : database.php
 * Project : Baca Di Teras
 * Version : 1.0.0
 *
 * Mendefinisikan konstanta untuk koneksi ke database MySQL (SLiMS).
 */

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'bacaditeras');
}

if (!defined('DB_PORT')) {
    define('DB_PORT', 3306);
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}
