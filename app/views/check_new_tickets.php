<?php
// Configuración inicial
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Conexión a BD con manejo de errores
try {
    require_once realpath(__DIR__ . '/../../config/db.php');
    
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos", 500);
    }

    // Consulta optimizada
    $query = "SELECT 
                t.id as ticket_id, 
                CONCAT('T-', LPAD(t.id, 4, '0')) as ticket_number, 
                e.nombre as specialty,
                UNIX_TIMESTAMP(t.fecha_hora) as timestamp
              FROM turnos t 
              JOIN especialidades e ON t.especialidad_id = e.id 
              WHERE t.estado = 'llamado' 
              ORDER BY t.fecha_hora DESC 
              LIMIT 1";

    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Error en la consulta SQL", 500);
    }

    $response = [
        'success' => true,
        'has_ticket' => false,
        'is_new' => false,
        'ticket_id' => null,
        'ticket_number' => null,
        'specialty' => null
    ];

    if (mysqli_num_rows($result) > 0) {
        $ticket = mysqli_fetch_assoc($result);
        $is_new = (time() - $ticket['timestamp']) < 120; // 2 minutos
        
        $response = [
            'success' => true,
            'has_ticket' => true,
            'is_new' => $is_new,
            'ticket_id' => (int)$ticket['ticket_id'],
            'ticket_number' => $ticket['ticket_number'],
            'specialty' => $ticket['specialty']
        ];
    }

    // Limpiar buffer de salida antes de enviar JSON
    if (ob_get_length()) ob_clean();
    echo json_encode($response);
    exit;

} catch (Exception $e) {
    // Respuesta de error estandarizada
    $errorResponse = [
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ];
    
    http_response_code(500);
    if (ob_get_length()) ob_clean();
    echo json_encode($errorResponse);
    exit;
}
?>