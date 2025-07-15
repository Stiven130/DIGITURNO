<?php
include('../controlers/db/db.php');

$query = "SELECT t.id, t.cedula, e.nombre AS especialidad, t.fecha_hora, t.estado
          FROM turnos t JOIN especialidades e ON t.especialidad_id = e.id 
          WHERE t.estado = 'pendiente'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "<div class='alert alert-danger'>Error al obtener los turnos: " . mysqli_error($conn) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepcionista - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #F7F9FC; /* Color de Fondo */
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center text-primary">Turnos Pendientes</h2>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cédula</th>
                    <th>Especialidad</th>
                    <th>Fecha y Hora</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while($turno = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $turno['id']; ?></td>
                        <td><?php echo $turno['cedula']; ?></td>
                        <td><?php echo $turno['especialidad']; ?></td>
                        <td><?php echo $turno['fecha_hora']; ?></td>
                        <td>
                            <form action="../controlers/llamar_turno.php" method="POST">
                                <input type="hidden" name="ticket_id" value="<?php echo $turno['id']; ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-phone-alt"></i> Llamar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>