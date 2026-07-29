<?php
/**
 * Logout Page
 */

$libPath = defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/../../..';
require_once $libPath . '/custom/services/AuthService.php';

$auth = new AuthService();
$auth->logout();

$base_url = defined('BASE_URL') ? BASE_URL : '/baca-di-teras';
header("Location: {$base_url}/portal-admin/login");
exit;
