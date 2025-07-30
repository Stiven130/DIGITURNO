<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['ticket_id'])) {
        throw new Exception("Solicitud inválida");
    }

    $ticket_id = intval($_POST['ticket_id']);
    $estado = $_POST['estado'] ?? 'llamado';
    $observacion = $_POST['observacion'] ?? null;

    // Valores exactos del ENUM
    $estadosPermitidos = ['llamado', 'atendido', 'no atendido'];
    if (!in_array($estado, $estadosPermitidos)) {
        throw new Exception("Estado no válido. Usar: 'llamado', 'atendido' o 'no atendido'");
    }

    if ($estado === 'llamado') {
        // Actualizar solo el estado (sin eliminar)
        $stmt = $conn->prepare("UPDATE turnos SET estado = 'llamado', fecha_hora = NOW() WHERE id = ?");
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Turno no disponible o ya procesado");
        }

        // Obtener datos para mostrar en pantalla
        $stmt = $conn->prepare("SELECT t.id, e.nombre AS especialidad 
                               FROM turnos t 
                               JOIN especialidades e ON t.especialidad_id = e.id 
                               WHERE t.id = ?");
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $turno = $result->fetch_assoc();
        
        echo json_encode(['success' => true, 'turno' => $turno]);

    } else {
        // Para 'atendido' o 'no atendido': actualizar estado y observación
        $stmt = $conn->prepare("UPDATE turnos 
                              SET estado = ?, 
                                  observacion = ?, 
                                  fecha_hora = NOW() 
                              WHERE id = ?");
        $stmt->bind_param("ssi", $estado, $observacion, $ticket_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No se pudo actualizar el turno");
        }

        echo json_encode(['success' => true]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>