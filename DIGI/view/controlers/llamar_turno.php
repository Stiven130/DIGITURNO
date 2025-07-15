<?php
include('./db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id'])) {
    $ticket_id = intval($_POST['ticket_id']);

    // Obtener datos del turno usando consultas preparadas
    $query = "SELECT t.*, e.nombre AS especialidad FROM turnos t JOIN especialidades e ON t.especialidad_id = e.id WHERE t.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $turno = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($turno) {
        // Cambiar estado a 'llamado' usando consultas preparadas
        $query_update = "UPDATE turnos SET estado='llamado' WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $ticket_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    } else {
        echo "<div class='alert alert-danger'>Turno no encontrado.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-warning'>No se ha seleccionado un turno.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Llamar Turno - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        setTimeout(() => location.reload(), 10000); // Refresca cada 10 segundos
    </script>
    <style>
        body {
            background-color: #F7F9FC; /* Color de Fondo */
            color: #333;
            font-family: 'Arial', sans-serif;
        }
        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }
        .card-header {
            background-color: #4A90E2; /* Color Primario */
            color: white;
            text-align: center;
        }
        .ticket-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-success, .btn-danger {
            width: 48%;
            margin: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Llamando Turno</h3>
            </div>
            <div class="card-body">
                <div class="ticket-info">
                    <h3 class="display-4">Ticket #<?php echo $turno['id']; ?></h3>
                    <p><strong>Cédula:</strong> <?php echo $turno['cedula']; ?></p>
                    <p><strong>Especialidad:</strong> <?php echo $turno['especialidad']; ?></p>
                    <p><strong>Fecha y Hora:</strong> <?php echo $turno['fecha_hora']; ?></p>
                </div>
                <form action="procesar_turno.php" method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $turno['id']; ?>">
                    <div class="mb-3">
                        <label for="observacion" class="form-label">Observación:</label>
                        <textarea name="observacion" class="form-control" rows="2" placeholder="Escriba aquí una observación..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="estado" value="atendido" class="btn btn-success btn-lg">Atendido</button>
                        <button type="submit" name="estado" value="no atendido" class="btn btn-danger btn-lg">No Atendido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <p>&copy; 2025 ENLACE DOS | Todos los derechos reservados</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>