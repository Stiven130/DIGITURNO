<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'digiturnos');
define('DB_CHARSET', 'utf8mb4');

// Establecer conexión con manejo de errores mejorado
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }
    
    // Establecer charset
    if (!$conn->set_charset(DB_CHARSET)) {
        throw new Exception("Error al establecer el charset: " . $conn->error);
    }
    
    // Configurar zona horaria si es necesario
    $conn->query("SET time_zone = '-05:00'"); // Ajusta según tu zona horaria
    
} catch (Exception $e) {
    // Registrar el error (útil para producción)
    error_log($e->getMessage());
    
    // Mostrar mensaje seguro (sin detalles sensibles)
    die("Error al conectar con la base de datos. Por favor, intente más tarde.");
}
?>