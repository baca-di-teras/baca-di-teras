<?php
require 'custom/helpers/Database.php';
$db = Database::getInstance();
$res = $db->fetchAll("SHOW TABLES");
foreach ($res as $row) {
    echo array_values($row)[0] . "\n";
}
