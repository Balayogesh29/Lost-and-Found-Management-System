<?php
// Update these values only if your local MySQL setup uses different credentials.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'lost_and_found');
define('DB_USER', 'root');
define('DB_PASS', '');

function db(): PDO
{
    static $connection = null;

    if ($connection === null) {
        $connection = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    return $connection;
}
