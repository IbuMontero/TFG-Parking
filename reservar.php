<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include("includes/db.php");

$id_usuario = $_SESSION['user_id'];
$rol = $_SESSION['rol'];

// --- Reservar plaza ---
if(isset($_POST['reservar'])){
    $id_plaza = $_POST['id_plaza'];
    $fecha = date('Y-m-d');
    $hora_inicio = date('H:i:s');
    $hora_fin = date('H:i:s', strtotime('+2 hours'));

    // Marcar la plaza como reservada
    $conn->query("UPDATE plazas SET estado='reservada' WHERE id=$id_plaza");

    // Insertar en tabla de reservas
    $conn->query("INSERT INTO reservas (id_usuario, id_plaza, fecha_reserva, hora_inicio, hora_fin)
                  VALUES ($id_usuario, $id_plaza, '$fecha', '$hora_inicio', '$hora_fin')");
}

// --- Consultar plazas libres ---
$sql = "SELECT * FROM plazas WHERE estado='libre' AND (reservado_para='ninguno' OR reservado_para='$rol')";
$plazas = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Plaza</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-container">
    <h2>Plazas Disponibles</h2>

    <?php if($plazas->num_rows > 0): ?>
        <table border="1" style="width:100%; border-collapse: collapse;">
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Reservado Para</th>
                <th>Acción</th>
            </tr>
            <?php while($p = $plazas->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= ucfirst($p['tipo']) ?></td>
                <td><?= ucfirst(str_replace('_',' ',$p['reservado_para'])) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id_plaza" value="<?= $p['id'] ?>">
                        <button type="submit" name="reservar">Reservar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay plazas disponibles en este momento.</p>
    <?php endif; ?>

    <br>
    <a href="panel.php"><button>Volver al Panel</button></a>
</div>
</body>
</html>
