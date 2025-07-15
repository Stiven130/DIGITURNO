<?php
include('../controlers/db/db.php');

$query = "SELECT t.id FROM turnos t WHERE t.estado = 'llamado' ORDER BY t.fecha_hora DESC LIMIT 3";
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
    <title>Pantalla de Llamados - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F7F9FC;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .turno-item {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 10px;
            text-align: center;
        }
        .video-container {
            width: 100%;
        }
        @media (min-width: 768px) {
            .video-container {
                width: 50%;
            }
        }
        .turnos-container {
            width: 100%;
        }
        @media (min-width: 768px) {
            .turnos-container {
                width: 50%;
            }
        }
        .flex-container {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center text-primary mb-4">Pantalla de Llamados</h2>

        <div class="flex-container">
            <div class="video-container">
                <h4>Video Promocional</h4>
                <video width="100%" height="auto" controls>
                    <source src="video.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <div class="turnos-container">
                <h4>Turnos Llamados</h4>
                <?php while($turno = mysqli_fetch_assoc($result)): ?>
                    <div class="turno-item">
                        <h5>Ticket #<?php echo $turno['id']; ?></h5>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>