<?php
require_once realpath(__DIR__ . '/../../config/db.php');

// Consulta para turnos pendientes (muestra estado 'pendiente y llamado')
$query = "SELECT t.id, t.cedula, e.nombre as especialidad, t.fecha_hora 
          FROM turnos t 
          JOIN especialidades e ON t.especialidad_id = e.id 
          WHERE t.estado IN ('pendiente', 'llamado')
          ORDER BY t.fecha_hora ASC";
$turnos = mysqli_query($conn, $query);

// Consulta MODIFICADA para últimos llamados (ahora incluye estado y observación)
$query_llamados = "SELECT t.id, t.cedula, e.nombre as especialidad, t.fecha_hora, 
                   t.estado, t.observacion 
                   FROM turnos t 
                   JOIN especialidades e ON t.especialidad_id = e.id 
                   WHERE t.estado IN ('atendido', 'no atendido')
                   ORDER BY t.fecha_hora DESC 
                   LIMIT 5";
$llamados = mysqli_query($conn, $query_llamados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Recepcionista - Digiturnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .table-responsive { max-height: 500px; overflow-y: auto; }
        .btn-action-group { display: flex; gap: 0.3rem; flex-wrap: nowrap; }
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            animation: fadeInOut 3s ease-in-out;
        }
        /* ESTILOS NUEVOS */
        .clickable-turno {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .clickable-turno:hover {
            background-color: #f0f0f0;
        }
        .badge-atendido { background-color: #28a745; }
        .badge-no-atendido { background-color: #dc3545; }
        @keyframes fadeInOut { 0%, 100% { opacity: 0; } 10%, 90% { opacity: 1; } }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="fas fa-user-md"></i> Panel Recepcionista</h1>
            <a href="../../public/index.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-list"></i> Turnos Pendientes
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N° Turno</th>
                                        <th>Cédula</th>
                                        <th>Especialidad</th>
                                        <th>Hora Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($turno = mysqli_fetch_assoc($turnos)): ?>
                                    <tr>
                                        <td>T-<?= str_pad($turno['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($turno['cedula']) ?></td>
                                        <td><?= htmlspecialchars($turno['especialidad']) ?></td>
                                        <td><?= date('H:i', strtotime($turno['fecha_hora'])) ?></td>
                                        <td>
                                            <div class="btn-action-group">
                                                <button onclick="llamarTurno(<?= $turno['id'] ?>)" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-bullhorn"></i> Llamar
                                                </button>
                                                <button onclick="abrirModalObservacion(<?= $turno['id'] ?>, 'atendido')" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Atendido
                                                </button>
                                                <button onclick="abrirModalObservacion(<?= $turno['id'] ?>, 'no atendido')" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i> No Atendido
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-history"></i> Últimos Turnos Llamados
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($llamado = mysqli_fetch_assoc($llamados)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center clickable-turno"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetalleTurno"
                                data-estado="<?= htmlspecialchars($llamado['estado']) ?>"
                                data-observacion="<?= htmlspecialchars($llamado['observacion'] ?? 'No se registraron observaciones') ?>">
                                T-<?= str_pad($llamado['id'], 3, '0', STR_PAD_LEFT) ?>
                                <span class="badge rounded-pill <?= $llamado['estado'] == 'atendido' ? 'badge-atendido' : 'badge-no-atendido' ?>">
                                    <?= date('H:i', strtotime($llamado['fecha_hora'])) ?>
                                </span>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Observaciones (EXISTENTE) -->
    <div class="modal fade" id="modalObservaciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Observaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formObservaciones">
                        <input type="hidden" id="turnoIdModal">
                        <input type="hidden" id="accionTurnoModal">
                        <div class="mb-3">
                            <label class="form-label">Detalles:</label>
                            <textarea class="form-control" id="observacionTurno" rows="3" placeholder="Ej: Paciente llegó tarde..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="procesarTurnoConObservacion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal NUEVO para Detalles del Turno -->
    <div class="modal fade" id="modalDetalleTurno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Turno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>Estado:</strong>
                        <span id="detalleEstado" class="badge ms-2"></span>
                    </div>
                    <div class="mb-3">
                        <strong>Observaciones:</strong>
                        <div class="alert alert-light mt-2" id="detalleObservacion"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para llamar turno (sin eliminar de pendientes)
        function llamarTurno(ticket_id) {
            fetch('../../public/llamar_turno.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `ticket_id=${ticket_id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Turno T-${data.turno.id} (${data.turno.especialidad}) llamado`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.error, 'danger');
                }
            });
        }

        // Función para abrir modal de observaciones
        function abrirModalObservacion(ticket_id, accion) {
            document.getElementById('turnoIdModal').value = ticket_id;
            document.getElementById('accionTurnoModal').value = accion;
            document.getElementById('modalTitle').textContent = 
                accion === 'no atendido' ? 'Registrar motivo de no atención' : 'Observaciones médicas';
            document.getElementById('observacionTurno').placeholder = 
                accion === 'no atendido' ? 'Ej: No pagó, llegó tarde...' : 'Ej: Diagnóstico, tratamiento...';
            
            new bootstrap.Modal(document.getElementById('modalObservaciones')).show();
        }

        // Función para enviar observaciones
        function procesarTurnoConObservacion() {
            const ticket_id = document.getElementById('turnoIdModal').value;
            const accion = document.getElementById('accionTurnoModal').value;
            const observacion = document.getElementById('observacionTurno').value;

            fetch('../../public/llamar_turno.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `ticket_id=${ticket_id}&estado=${accion}&observacion=${encodeURIComponent(observacion)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(
                        accion === 'atendido' ? 'Turno atendido' : 'Turno marcado como no atendido', 
                        'success'
                    );
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.error, 'danger');
                }
            });
        }

        // Mostrar notificación
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} notification`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> 
                ${message}
            `;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        // FUNCIONALIDAD NUEVA: Cargar detalles al hacer clic en turnos
        document.addEventListener('DOMContentLoaded', function() {
            const modalDetalle = document.getElementById('modalDetalleTurno');
            
            if (modalDetalle) {
                modalDetalle.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const estado = button.getAttribute('data-estado');
                    const observacion = button.getAttribute('data-observacion');
                    
                    const estadoElement = document.getElementById('detalleEstado');
                    const observacionElement = document.getElementById('detalleObservacion');
                    
                    // Configurar estado
                    if (estado === 'atendido') {
                        estadoElement.className = 'badge bg-success';
                        estadoElement.textContent = 'Atendido';
                    } else {
                        estadoElement.className = 'badge bg-danger';
                        estadoElement.textContent = 'No Atendido';
                    }
                    
                    // Configurar observación
                    observacionElement.textContent = observacion;
                });
            }
        });
    </script>
</body>
</html>