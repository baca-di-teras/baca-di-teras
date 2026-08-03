<?php
require 'custom/helpers/Database.php';
require 'custom/services/NewsService.php';
$ns = new NewsService();
print_r($ns->getFeaturedNews());
