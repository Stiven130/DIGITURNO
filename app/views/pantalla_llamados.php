<?php
// Configuración de la base de datos
$db_path = realpath(__DIR__ . '/../../config/db.php');
if (file_exists($db_path)) {
    include($db_path);
} else {
    die("<div class='alert alert-danger'>Error: Archivo de configuración no encontrado</div>");
}

if (!isset($conn) || !$conn) {
    die("<div class='alert alert-danger'>Error de conexión a la base de datos</div>");
}

// Obtener turnos llamados
$query = "SELECT t.id, t.cedula, e.nombre AS especialidad, e.id as especialidad_id, 
          t.fecha_hora, t.estado 
          FROM turnos t 
          JOIN especialidades e ON t.especialidad_id = e.id 
          WHERE t.estado = 'llamado' 
          ORDER BY t.fecha_hora DESC LIMIT 5";
$result = mysqli_query($conn, $query);

// Obtener estadísticas (adaptado para tu estructura de BD)
$stats_query = "SELECT 
                COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
                COUNT(CASE WHEN estado = 'llamado' THEN 1 END) as llamados,
                COUNT(CASE WHEN estado = 'atendido' THEN 1 END) as atendidos,
                AVG(TIMESTAMPDIFF(MINUTE, fecha_hora, NOW())) as espera_promedio
                FROM turnos";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Llamados - ENLACE DOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #b21f1f;
            --accent-color: #fdbb2d;
            --text-light: #ffffff;
            --text-dark: #333333;
            --bg-dark: #121212;
            --bg-light: #f8f9fa;
        }
        
        body {
            background-color: var(--bg-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-light);
            overflow-x: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color), var(--accent-color));
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: relative;
            z-index: 100;
        }
        
        .logo {
            height: 60px;
            transition: all 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
        
        .digital-clock {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
            line-height: 1;
        }
        
        .current-date {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .main-container {
            padding: 20px 0;
        }
        
        .current-ticket {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 8px solid var(--accent-color);
            transition: all 0.5s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .current-ticket h2 {
            color: var(--accent-color);
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .ticket-number {
            font-size: 5rem;
            font-weight: 900;
            line-height: 1;
            color: var(--text-light);
            text-shadow: 0 0 15px rgba(255,255,255,0.3);
            margin: 20px 0;
        }
        
        .ticket-specialty {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-light);
        }
        
        .next-tickets-title {
            font-size: 1.8rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
            display: inline-block;
        }
        
        .ticket-card {
            background: rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-left: 4px solid;
            height: 100%;
        }
        
        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .ticket-card .ticket-id {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .ticket-card .ticket-specialty {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .stats-card {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-card .stats-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .stats-card .stats-label {
            font-size: 1rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .video-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-top: 30px;
        }
        
        .alert-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            animation: slideIn 0.5s forwards;
            max-width: 400px;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(253, 187, 45, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(253, 187, 45, 0); }
            100% { box-shadow: 0 0 0 0 rgba(253, 187, 45, 0); }
        }
        
        .pulse-effect {
            animation: pulse 2s infinite;
        }
        
        /* Colores por especialidad */
        .specialty-1 { border-color: #4A90E2; }
        .specialty-2 { border-color: #50E3C2; }
        .specialty-3 { border-color: #F5A623; }
        .specialty-4 { border-color: #E74C3C; }
        .specialty-5 { border-color: #9B59B6; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .ticket-number {
                font-size: 3.5rem;
            }
            
            .ticket-specialty {
                font-size: 1.5rem;
            }
            
            .digital-clock {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Cabecera -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="../../public/assets/img/logo-blanco.png" alt="ENLACE DOS" class="logo">
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="digital-clock" id="digitalClock"></div>
                    <div class="current-date" id="currentDate"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <div class="container">
            <!-- Turno Actual -->
            <div class="current-ticket animate__animated animate__fadeIn pulse-effect" id="currentTicket">
                <h2><i class="fas fa-bell"></i> TURNO ACTUAL</h2>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $current_ticket = mysqli_fetch_assoc($result); ?>
                    <div class="ticket-number">T-<?php echo str_pad($current_ticket['id'], 4, '0', STR_PAD_LEFT); ?></div>
                    <div class="ticket-specialty">
                        <i class="fas fa-user-md"></i> <?php echo $current_ticket['especialidad']; ?>
                    </div>
                    <div class="text-muted mt-2">
                        <?php echo date('H:i', strtotime($current_ticket['fecha_hora'])); ?>
                    </div>
                <?php else: ?>
                    <div class="ticket-number">---</div>
                    <div class="ticket-specialty">No hay turnos activos</div>
                <?php endif; ?>
            </div>
            
            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-value"><?php echo $stats['pendientes']; ?></div>
                        <div class="stats-label">Pendientes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-value"><?php echo $stats['llamados']; ?></div>
                        <div class="stats-label">Llamados</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-value"><?php echo $stats['atendidos']; ?></div>
                        <div class="stats-label">Atendidos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-value"><?php echo round($stats['espera_promedio']); ?>'</div>
                        <div class="stats-label">Espera Promedio</div>
                    </div>
                </div>
            </div>
            
            <!-- Próximos Turnos -->
            <h3 class="next-tickets-title"><i class="fas fa-list-ol"></i> PRÓXIMOS TURNOS</h3>
            <div class="row" id="nextTickets">
                <?php while($ticket = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-3">
                        <div class="ticket-card specialty-<?php echo $ticket['especialidad_id'] % 5 + 1; ?>">
                            <div class="ticket-id">T-<?php echo $ticket['id']; ?></div>
                            <div class="ticket-specialty">
                                <i class="fas fa-user-md"></i> <?php echo $ticket['especialidad']; ?>
                            </div>
                            <div class="ticket-time">
                                <small><?php echo date('H:i', strtotime($ticket['fecha_hora'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($result) === 0): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No hay más turnos en cola
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Video Promocional -->
            <div class="video-container">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1&mute=1&loop=1&controls=0" 
                            allowfullscreen allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </main>

    <!-- Notificación de nuevo turno -->
    <div class="alert-notification alert alert-success d-none" id="newTicketAlert">
        <strong>Nuevo turno llamado!</strong> <span id="newTicketText"></span>
    </div>

    <!-- Audio para notificaciones -->
    <audio id="notificationSound" src="../../public/assets/sonidos/notification.mp3"></audio>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        // Reloj en tiempo real
        function updateClock() {
            var now = new Date();
            var timeStr = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            var dateStr = now.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            $('#digitalClock').text(timeStr);
            $('#currentDate').text(dateStr.charAt(0).toUpperCase() + dateStr.slice(1));
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Actualización automática de turnos
        var lastTicketId = <?php echo isset($current_ticket['id']) ? $current_ticket['id'] : 0; ?>;
        
        function checkNewTickets() {
            $.ajax({
                url: 'check_new_tickets.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.has_ticket && data.is_new && data.ticket_id !== lastTicketId) {
                        lastTicketId = data.ticket_id;
                        showNewTicketNotification(data.ticket_number, data.specialty);
                        playNotificationSound();
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error al verificar turnos:", error);
                    setTimeout(function() {
                        location.reload();
                    }, 120000);
                }
            });
        }

        // Mostrar notificación de nuevo turno
        function showNewTicketNotification(ticketNumber, specialty) {
            $('#newTicketText').text('Turno ' + ticketNumber + ' - ' + specialty);
            $('#newTicketAlert').removeClass('d-none').addClass('animate__animated animate__bounceIn');
            
            setTimeout(function() {
                $('#newTicketAlert').addClass('animate__animated animate__fadeOutRight');
                setTimeout(function() {
                    $('#newTicketAlert').addClass('d-none').removeClass('animate__fadeOutRight');
                }, 1000);
            }, 5000);
        }
        
        // Reproducir sonido de notificación
        function playNotificationSound() {
            var audio = document.getElementById('notificationSound');
            audio.currentTime = 0;
            audio.play().catch(function(e) {
                console.log("No se pudo reproducir el sonido:", e);
            });
        }
        
        // Verificar nuevos turnos cada 10 segundos
        setInterval(checkNewTickets, 10000);
        
        // Pantalla completa automática
        function enterFullscreen() {
            try {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(function(e) {
                        console.log(e);
                    });
                }
            } catch (e) {
                console.log("Pantalla completa no soportada:", e);
            }
        }
        
        // Entrar en pantalla completa al cargar
        $(document).ready(function() {
            enterFullscreen();
            $('#currentTicket').addClass('animate__pulse');
        });
        
        // Volver a pantalla completa si el usuario sale
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                enterFullscreen();
            }
        });
    </script>
</body>
</html>