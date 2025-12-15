<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include("includes/db.php");

$id_usuario = $_SESSION['user_id'];

// --- Cancelar reserva ---
if(isset($_POST['cancelar'])){
    $id_reserva = $_POST['id_reserva'];

    // Obtener la plaza asociada
    $res = $conn->query("SELECT id_plaza FROM reservas WHERE id=$id_reserva AND id_usuario=$id_usuario");
    if($res->num_rows > 0){
        $plaza = $res->fetch_assoc()['id_plaza'];
        // Eliminar reserva y liberar plaza
        $conn->query("DELETE FROM reservas WHERE id=$id_reserva");
        $conn->query("UPDATE plazas SET estado='libre' WHERE id=$plaza");
    }
}

// --- Consultar reservas activas ---
$sql = "SELECT r.id, p.tipo, p.reservado_para, r.fecha_reserva, r.hora_inicio, r.hora_fin 
        FROM reservas r 
        JOIN plazas p ON r.id_plaza = p.id 
        WHERE r.id_usuario = $id_usuario";
$reservas = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-container">
    <h2>Mis Reservas Actuales</h2>

    <?php if($reservas->num_rows > 0): ?>
        <table border="1" style="width:100%; border-collapse: collapse;">
            <tr>
                <th>ID Reserva</th>
                <th>Tipo Plaza</th>
                <th>Reservado Para</th>
                <th>Fecha</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Acción</th>
            </tr>
            <?php while($r = $reservas->fetch_assoc()): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= ucfirst($r['tipo']) ?></td>
                <td><?= ucfirst(str_replace('_',' ',$r['reservado_para'])) ?></td>
                <td><?= $r['fecha_reserva'] ?></td>
                <td><?= $r['hora_inicio'] ?></td>
                <td><?= $r['hora_fin'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id_reserva" value="<?= $r['id'] ?>">
                        <button type="submit" name="cancelar" style="background:red;">Cancelar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tienes reservas activas.</p>
    <?php endif; ?>

    <br>
    <a href="panel.php"><button>Volver al Panel</button></a>
</div>
</body>
</html>
