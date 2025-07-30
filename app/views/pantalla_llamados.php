<?php
// Configuración de la base de datos
$db_path = realpath(__DIR__ . '/../../config/db.php');
if (file_exists($db_path)) {
    include($db_path);
} else {
    die("<div class='alert alert-danger'>Error: Archivo de configuración no encontrado</div>");
}

if (!isset($conn) || $conn->connect_error) {
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

// Obtener el último turno llamado para el sistema de actualización
$current_ticket = mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : null;

// Definir rutas base correctamente (ajustadas a tu estructura)
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/digiturnos/';
$assets_url = $base_url . 'public/assets/';
$check_tickets_url = $base_url . 'app/views/check_new_tickets.php';
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
            --primary-color: #4f9993;
            --secondary-color: #65878b;
            --accent-color: #0e1418;
            --text-light: #ffffff;
            --bg-dark: #121212;
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
        }
        
        .logo {
            height: 60px;
            transition: all 0.3s ease;
        }
        
        .digital-clock {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        
        .current-date {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .current-ticket {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 8px solid var(--accent-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .ticket-number {
            font-size: 5rem;
            font-weight: 900;
            color: var(--text-light);
            text-shadow: 0 0 15px rgba(255,255,255,0.3);
            margin: 20px 0;
        }
        
        .ticket-specialty {
            font-size: 2rem;
            font-weight: 600;
        }
        
        .ticket-card {
            background: rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid;
        }
        
        .video-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-top: 30px;
        }
        
        /* Animaciones */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        /* Colores por especialidad */
        .specialty-1 { border-color: #4A90E2; }
        .specialty-2 { border-color: #50E3C2; }
        .specialty-3 { border-color: #F5A623; }
        .specialty-4 { border-color: #E74C3C; }
        .specialty-5 { border-color: #9B59B6; }
        
        @media (max-width: 768px) {
            .ticket-number { font-size: 3.5rem; }
            .ticket-specialty { font-size: 1.5rem; }
            .digital-clock { font-size: 2rem; }
        }

        /* Notificación flotante */
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
    </style>
</head>
<body>
    <!-- Cabecera -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="<?= $assets_url ?>img/logo-blanco.png" alt="ENLACE DOS" class="logo">
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="digital-clock" id="digitalClock"></div>
                    <div class="current-date" id="currentDate"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="container py-4">
        <!-- Turno Actual -->
        <div class="current-ticket animate__animated animate__fadeIn pulse-animation" id="currentTicket">
            <h2><i class="fas fa-bell"></i> TURNO ACTUAL</h2>
            <?php if($current_ticket): ?>
                <div class="ticket-number">T-<?= str_pad($current_ticket['id'], 4, '0', STR_PAD_LEFT) ?></div>
                <div class="ticket-specialty">
                    <i class="fas fa-user-md"></i> <?= $current_ticket['especialidad'] ?>
                </div>
                <div class="text-muted mt-2">
                    <?= date('H:i', strtotime($current_ticket['fecha_hora'])) ?>
                </div>
            <?php else: ?>
                <div class="ticket-number">---</div>
                <div class="ticket-specialty">No hay turnos activos</div>
            <?php endif; ?>
        </div>
        
        <!-- Próximos Turnos -->
        <h3 class="text-white mb-4"><i class="fas fa-list-ol"></i> TURNOS LLAMADOS</h3>
        <div class="row" id="nextTickets">
            <?php 
            if($current_ticket) {
                // Mostrar los siguientes turnos (ya obtuvimos el primero)
                while($ticket = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-3">
                        <div class="ticket-card specialty-<?= $ticket['especialidad_id'] % 5 + 1 ?>">
                            <div class="ticket-id">T-<?= $ticket['id'] ?></div>
                            <div class="ticket-specialty">
                                <i class="fas fa-user-md"></i> <?= $ticket['especialidad'] ?>
                            </div>
                            <div class="ticket-time">
                                <small><?= date('H:i', strtotime($ticket['fecha_hora'])) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            } else {
                echo '<div class="col-12"><div class="alert alert-info">No hay turnos en cola</div></div>';
            }
            ?>
        </div>
        
        <!-- Video Promocional -->
        <div class="video-container">
            <div class="ratio ratio-16x9">
                <video autoplay muted loop playsinline>
                    <source src="<?= $assets_url ?>videos/promocional.mp4" type="video/mp4">
                    Tu navegador no soporta el elemento de video.
                </video>
            </div>
        </div>
    </main>

    <!-- Notificación Toast -->
    <div class="notification-toast toast align-items-center text-white bg-success border-0 d-none" role="alert" aria-live="assertive" aria-atomic="true" id="liveToast">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-bell me-2"></i>
                <span id="toastMessage">Nuevo turno llamado!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <!-- Audio para notificaciones -->
    <audio id="notificationSound" src="<?= $assets_url ?>sonidos/notification.mp3"></audio>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentTicketId = <?= $current_ticket ? $current_ticket['id'] : 0 ?>;
        const toastLiveExample = document.getElementById('liveToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
        
        // Reloj en tiempo real
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('es-ES', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            document.getElementById('digitalClock').textContent = timeStr;
            document.getElementById('currentDate').textContent = 
                dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Función mejorada para verificar nuevos turnos
        function checkForNewTickets() {
            const timestamp = new Date().getTime();
            const url = `<?= $check_tickets_url ?>?last_id=${currentTicketId}&_=${timestamp}`;
            
            fetch(url, {
                headers: {
                    'Accept': 'application/json'
                },
                cache: 'no-store'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new TypeError("La respuesta no es JSON válido");
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error en la respuesta del servidor');
                }
                
                if (data.has_ticket && data.is_new && data.ticket_id !== currentTicketId) {
                    currentTicketId = data.ticket_id;
                    playNotificationSound();
                    showToastNotification(`Turno ${data.ticket_number} - ${data.specialty}`);
                    
                    // Recargar después de 3 segundos para dar tiempo a ver la notificación
                    setTimeout(() => location.reload(), 3000);
                }
            })
            .catch(error => {
                console.error('Error al verificar turnos:', error.message);
                // Reintentar después de 5 segundos en caso de error
                setTimeout(checkForNewTickets, 5000);
            });
        }

        // Mostrar notificación toast
        function showToastNotification(message) {
            toastMessage.textContent = message;
            toastLiveExample.classList.remove('d-none');
            toastBootstrap.show();
            
            // Ocultar después de 5 segundos
            setTimeout(() => {
                toastLiveExample.classList.add('d-none');
            }, 5000);
        }

        // Reproducir sonido de notificación
        function playNotificationSound() {
            const audio = document.getElementById('notificationSound');
            audio.currentTime = 0;
            audio.play().catch(e => console.log("Error al reproducir sonido:", e));
        }

        // Iniciar verificación de nuevos turnos
        checkForNewTickets();
        // Verificar cada 5 segundos
        setInterval(checkForNewTickets, 5000);

        // Efecto de animación para el turno actual
        setInterval(() => {
            document.getElementById('currentTicket').classList.toggle('animate__pulse');
        }, 3000);
    </script>
</body>
</html>