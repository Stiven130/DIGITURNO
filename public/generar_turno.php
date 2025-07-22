<?php
$db_path = realpath(__DIR__ . '/../config/db.php');
if (file_exists($db_path)) {
    include($db_path);
} else {
    die("<div class='alert alert-danger'>Error: Archivo de configuración no encontrado</div>");
}

// (Aquí iría tu lógica para generar turnos)
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
            background-color: #F7F9FC;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 30px;
            max-width: 600px;
        }
        .btn-return {
            background-color: #4A90E2;
            color: white;
            margin-bottom: 20px;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .btn-return:hover {
            background-color: #3a7bc8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botón de regreso -->
        <a href="index.php" class="btn btn-return">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>

        <h2 class="text-center text-primary mb-4">Generar Nuevo Turno</h2>
        
        <!-- (Aquí iría tu formulario para generar turnos) -->
        <div class="card shadow">
            <div class="card-body">
                <form action="procesar_turno.php" method="POST">
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                    </div>
                    <div class="mb-3">
                        <label for="especialidad" class="form-label">Especialidad</label>
                        <select class="form-select" id="especialidad" name="especialidad" required>
                            <option value="">Seleccione...</option>
                            <?php
                            $query = "SELECT * FROM especialidades";
                            $especialidades = mysqli_query($conn, $query);
                            while ($esp = mysqli_fetch_assoc($especialidades)) {
                                echo "<option value='{$esp['id']}'>{$esp['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-ticket-alt"></i> Generar Turno
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>