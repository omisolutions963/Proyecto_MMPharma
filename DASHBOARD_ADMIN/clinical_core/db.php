<?php
/**
 * MMPharma — Conexión PDO centralizada (Admin Panel)
 * Todos los módulos del admin deben incluir este archivo.
 * Segura para múltiples includes.
 */
require_once __DIR__ . '/../../includes/env_loader.php';
loadEnv(__DIR__ . '/../../.env');

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3307');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'mm_pharma');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

if (!function_exists('getDB')) {
 function getDB(): PDO {
 static $pdo = null;
 if ($pdo === null) {
 try {
 $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4';
 $pdo = new PDO($dsn, DB_USER, DB_PASS, [
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES => false,
 ]);
 } catch (PDOException $e) {
 http_response_code(500);
 die(json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]));
 }
 }
 return $pdo;
 }
}

if (!function_exists('getAppURL')) {
    function getAppURL() {
        $env_url = getenv('APP_URL');
        if ($env_url) {
            return rtrim($env_url, '/');
        }
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $subfolder = '';
        if (stripos($script, '/Proyecto_MMPharma') === 0) {
            $subfolder = '/Proyecto_MMPharma';
        }
        return $protocol . '://' . $host . $subfolder;
    }
}
