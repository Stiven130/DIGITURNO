<?php
// Conexión a BD (ajustada a tu estructura)
$db_path = realpath(__DIR__ . '/../config/db.php');
if (file_exists($db_path)) {
    include($db_path);
} else {
    die("<div class='alert alert-danger'>Error: Archivo de configuración no encontrado</div>");
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar inputs
    $cedula = mysqli_real_escape_string($conn, $_POST['cedula']);
    $especialidad_id = intval($_POST['especialidad']);
    
    // Validar cédula (ejemplo básico)
    if (empty($cedula) || strlen($cedula) < 6) {
        $error = "La cédula debe tener al menos 6 caracteres";
    } else {
        // Insertar turno (CON CORRECCIÓN PARA EVITAR BLOQUEOS)
        $query = "INSERT INTO turnos (cedula, especialidad_id, estado, fecha_hora) 
                 VALUES (?, ?, 'pendiente', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $cedula, $especialidad_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $ticket_id = mysqli_insert_id($conn);
            $success = "Turno generado exitosamente (N° $ticket_id)";
        } else {
            $error = "Error al generar el turno: " . mysqli_error($conn);
        }
        
        // CERRAR STATEMENT CORRECTAMENTE (ÚNICO CAMBIO NECESARIO)
        if (isset($stmt) && is_object($stmt)) {
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Turno - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 2rem;
            max-width: 600px;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .btn-return {
            background-color: #4a90e2;
            color: white;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        .btn-return:hover {
            background-color: #3a7bc8;
            color: white;
            transform: translateY(-2px);
        }
        .btn-submit {
            background-color: #28a745;
            padding: 10px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botón de regreso -->
        <a href="index.php" class="btn btn-return">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>

        <!-- Alertas -->
        <?php if(isset($error)): ?>
            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 class="text-center text-primary mb-4">
                <i class="fas fa-ticket-alt"></i> Generar Nuevo Turno
            </h2>
            
            <form action="generar_turno.php" method="POST">
                <div class="mb-4">
                    <label for="cedula" class="form-label">Número de Cédula</label>
                    <input type="text" class="form-control form-control-lg" id="cedula" name="cedula" 
                           placeholder="Ingrese su cédula" required>
                </div>
                
                <div class="mb-4">
                    <label for="especialidad" class="form-label">Seleccione Especialidad</label>
                    <select class="form-select form-select-lg" id="especialidad" name="especialidad" required>
                        <option value="">-- Seleccione una especialidad --</option>
                        <?php
                        $query = "SELECT * FROM especialidades ORDER BY nombre";
                        $especialidades = mysqli_query($conn, $query);
                        while ($esp = mysqli_fetch_assoc($especialidades)) {
                            echo "<option value='{$esp['id']}'>{$esp['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-submit btn-lg w-100">
                    <i class="fas fa-check-circle"></i> Generar Turno
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación básica de cédula en cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            const cedula = document.getElementById('cedula').value;
            if (cedula.length < 6) {
                alert('La cédula debe tener al menos 6 caracteres');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>