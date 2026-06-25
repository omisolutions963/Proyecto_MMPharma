<?php
// includes/env_loader.php
// Cargador ligero de variables de entorno para PHP en XAMPP

if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Ignorar líneas vacías y comentarios
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            
            // Parsear clave=valor
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                
                // Quitar comillas si existen al inicio/final del valor
                if ((strpos($val, '"') === 0 && strrpos($val, '"') === strlen($val) - 1) || 
                    (strpos($val, "'") === 0 && strrpos($val, "'") === strlen($val) - 1)) {
                    $val = substr($val, 1, -1);
                }
                
                // Definir en variables del sistema si no están predefinidas
                if (getenv($key) === false) {
                    putenv("$key=$val");
                }
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $val;
                }
                if (!isset($_SERVER[$key])) {
                    $_SERVER[$key] = $val;
                }
            }
        }
    }
}
