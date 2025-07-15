<?php
include('../controlers/db/db.php');

// Manejo de errores de conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener la fecha y hora actual
$fecha = date('Y-m-d');
$hora = date('H:i:s');

// Obtener las especialidades para el dropdown
$query_especialidades = "SELECT * FROM especialidades";
$result_especialidades = mysqli_query($conn, $query_especialidades);

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = mysqli_real_escape_string($conn, $_POST['cedula']);
    $especialidad_id = intval($_POST['especialidad']);

    // Insertar el turno usando consultas preparadas
    $query_insert = "INSERT INTO turnos (cedula, especialidad_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query_insert);
    mysqli_stmt_bind_param($stmt, "si", $cedula, $especialidad_id);

    if (mysqli_stmt_execute($stmt)) {
        $ticket_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        header('Location: generar_turno.php?ticket=' . $ticket_id);
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al generar el ticket: " . mysqli_error($conn) . "</div>";
    }
    mysqli_stmt_close($stmt);
}

// Obtener información del ticket si está presente en la URL
if (isset($_GET['ticket'])) {
    $ticket_id = intval($_GET['ticket']);
    $ticket_query = "SELECT t.id, t.cedula, e.nombre AS especialidad, t.fecha_hora FROM turnos t JOIN especialidades e ON t.especialidad_id = e.id WHERE t.id = ?";
    $stmt = mysqli_prepare($conn, $ticket_query);
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);
    mysqli_stmt_execute($stmt);
    $ticket_result = mysqli_stmt_get_result($stmt);
    $ticket_data = mysqli_fetch_assoc($ticket_result);
    mysqli_stmt_close($stmt);
}

// Cerrar la conexión
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Turno - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #F7F9FC; /* Color de Fondo */
            font-family: 'Arial', sans-serif;
        }
        .ticket {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .ticket-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function printTicket() {
            var printContent = document.getElementById('ticketInfo').innerHTML;
            var newWindow = window.open('', '', 'height=600,width=800');
            newWindow.document.write('<html><head><title>Ticket</title>');
            newWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
            newWindow.document.write('</head><body>');
            newWindow.document.write(printContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($ticket_data)) { ?>
            <div id="ticketInfo">
                <div class="ticket-print">
                    <div class="header">
                        <img src="logo.png" alt="ENLACE DOS" class="img-fluid" style="max-width: 100px;">
                        <h3>ENLACE DOS</h3>
                    </div>
                    <div class="ticket-info">
                        <p><strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s', strtotime($ticket_data['fecha_hora'])); ?></p>
                        <p><strong>Ticket N°:</strong> <?php echo $ticket_data['id']; ?></p>
                        <p><strong>Cédula:</strong> <?php echo $ticket_data['cedula']; ?></p>
                        <p><strong>Especialidad:</strong> <?php echo $ticket_data['especialidad']; ?></p>
                    </div>
                </div>
                <button class="btn btn-primary mt-3" onclick="printTicket()"><i class="fas fa-print"></i> Imprimir Ticket</button>
            </div>
        <?php } else { ?>
            <div class="ticket">
                <div class="header">
                    <img src="logo.png" alt="ENLACE DOS" class="img-fluid" style="max-width: 100px;">
                    <h3>ENLACE DOS</h3>
                </div>
                <div class="ticket-info">
                    <p><strong>Fecha:</strong> <?php echo $fecha; ?> | <strong>Hora:</strong> <?php echo $hora; ?></p>
                </div>
                <form method="POST" action="generar_turno.php">
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Número de Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required placeholder="Ingresa tu número de cédula">
                    </div>
                    <div class="mb-3">
                        <label for="especialidad" class="form-label">Especialidad</label>
                        <select class="form-select" id="especialidad" name="especialidad" required>
                            <option value="">Seleccione una especialidad</option>
                            <?php while ($row = mysqli_fetch_assoc($result_especialidades)) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Generar Ticket</button>
                </form>
            </div>
        <?php } ?>
    </div>
    <div class="text-center mt-4">
        <p>&copy; 2025 ENLACE DOS | Todos los derechos reservados</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>