<?php
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', '3307');
if (!defined('DB_NAME')) define('DB_NAME', 'mm_pharma');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

if (!function_exists('getDB')) {
    function getDB() {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ));
        }
        return $pdo;
    }
}

if (!function_exists('roundStat')) {
    /**
     * Redondea un número hacia abajo al centenar o decena más cercana
     * y añade un prefijo '+' si aplica.
     */
    function roundStat($n) {
        if ($n < 10) return $n;
        if ($n < 100) return '+' . (floor($n / 10) * 10);
        return '+' . (floor($n / 100) * 100);
    }
}

