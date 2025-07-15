<?php
include('../controlers/db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id'], $_POST['estado'])) {
    $ticket_id = $_POST['ticket_id'];
    $estado = $_POST['estado'];
    $observacion = isset($_POST['observacion']) ? $_POST['observacion'] : '';

    $query = "UPDATE turnos SET estado='$estado', observacion='$observacion' WHERE id=$ticket_id";
    mysqli_query($conn, $query);

    echo "<script>
            alert('Turno actualizado correctamente');
            window.location.href='../includes/recepcionista.php';
          </script>";
} else {
    echo "<script>alert('Error en la solicitud.'); window.history.back();</script>";
}
?>
