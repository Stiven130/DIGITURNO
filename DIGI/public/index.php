<?php
include('../view/controlers/db/db.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digiturnos - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #F7F9FC; /* Color de Fondo */
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #4A90E2; /* Color Primario */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .card {
            border-radius: 12px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #50E3C2; /* Color Secundario */
        }
        .btn-success {
            background-color: #7ED321; /* Color de Éxito */
        }
        .btn-warning {
            background-color: #F5A623; /* Color de Advertencia */
        }
        .btn-info {
            background-color: #4A90E2; /* Color Primario */
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/logo.png" alt="Logo" height="40"> ENLACE DOS
            </a>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h1 class="text-center text-primary mb-4">Bienvenido a Digiturnos</h1>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-ticket-alt feature-icon"></i>
                        <h4 class="text-primary">Generar Turno</h4>
                        <p>Solicita tu turno fácilmente.</p>
                        <a href="../view/controlers/generar_turno.php" class="btn btn-success btn-lg w-100">Ingresar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-user-nurse feature-icon"></i>
                        <h4 class="text-warning">Recepción</h4>
                        <p>Gestiona los turnos de los pacientes.</p>
                        <a href="../view/includes/recepcionista.php" class="btn btn-warning btn-lg w-100">Ingresar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-tv feature-icon"></i>
                        <h4 class="text-info">Pantalla de Llamados</h4>
                        <p>Visualiza los turnos llamados en tiempo real.</p>
                        <a href="../view/includes/pantalla_llamados.php" class="btn btn-info btn-lg w-100">Ingresar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>