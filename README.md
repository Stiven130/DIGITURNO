# DIGITURNO

// Consulta para turnos pendientes (muestra estado 'pendiente y llamado')
$query = "SELECT t.id, t.cedula, e.nombre as especialidad, t.fecha_hora 
          FROM turnos t 
          JOIN especialidades e ON t.especialidad_id = e.id 
          WHERE t.estado IN ('pendiente', 'llamado')
          ORDER BY t.fecha_hora ASC";
$turnos = mysqli_query($conn, $query);

// Consulta para últimos llamados (estado 'atendido' o 'no atendido')
$query_llamados = "SELECT t.id, t.cedula, e.nombre as especialidad, t.fecha_hora 
                   FROM turnos t 
                   JOIN especialidades e ON t.especialidad_id = e.id 
                   WHERE t.estado IN ('atendido', 'no atendido')
                   ORDER BY t.fecha_hora DESC 
                   LIMIT 5";
$llamados = mysqli_query($conn, $query_llamados);