<?php
declare(strict_types=1);

define('APP_NAME', 'GameTopUp');
define('BASE_URL', '/gametopup'); // Ubah jika nama folder atau virtual host berbeda.
date_default_timezone_set('Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
