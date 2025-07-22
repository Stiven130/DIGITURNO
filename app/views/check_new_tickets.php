<?php
include(__DIR__ . '/../../config/db.php');

header('Content-Type: application/json');

// Obtener el último turno llamado
$query = "SELECT t.id as ticket_id, 
          CONCAT('T-', LPAD(t.id, 4, '0')) as ticket_number, 
          e.nombre as specialty,
          t.fecha_hora as called_time
          FROM turnos t 
          JOIN especialidades e ON t.especialidad_id = e.id 
          WHERE t.estado = 'llamado' 
          ORDER BY t.fecha_hora DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$latest_ticket = mysqli_fetch_assoc($result);

// Verificar si el turno fue llamado en los últimos 2 minutos
$is_new = false;
if ($latest_ticket) {
    $called_time = strtotime($latest_ticket['called_time']);
    $is_new = (time() - $called_time) < 120; // 120 segundos = 2 minutos
}

echo json_encode([
    'has_ticket' => mysqli_num_rows($result) > 0,
    'is_new' => $is_new,
    'ticket_id' => $latest_ticket['ticket_id'] ?? null,
    'ticket_number' => $latest_ticket['ticket_number'] ?? null,
    'specialty' => $latest_ticket['specialty'] ?? null
]);
?>