<?php
require 'c:/xampp/htdocs/baca-di-teras/custom/helpers/Database.php';
$db = Database::getInstance();
print_r($db->fetchAll('DESCRIBE biblio'));
print_r($db->fetchAll('SELECT * FROM biblio LIMIT 1'));

