<?php
/**
 * ============================================================
 * Congreso Internacional de Tecnología e Innovación
 * en Ingeniería y Computación
 * ------------------------------------------------------------
 * Conexión a la base de datos.
 *
 * En cPanel: Bases de datos > Bases de datos MySQL, crea la
 * base, el usuario y asígnale todos los privilegios. Luego
 * reemplaza los 4 valores de abajo con esos datos.
 * ============================================================
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_usuario_congreso_tic'); // ej: rcastill_congreso_tic
define('DB_USER', 'tu_usuario_dbuser');        // ej: rcastill_congreso
define('DB_PASS', 'tu_contraseña_aqui');

// Cupo máximo de inscripciones para el congreso.
// Cámbialo aquí y se actualiza en todo el sitio.
define('CUPO_MAXIMO', 150);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conexion->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('No se pudo conectar a la base de datos. Revisa las credenciales en config.php.');
}
