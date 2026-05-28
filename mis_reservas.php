<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("includes/db.php");

$id_usuario = $_SESSION['user_id'];

// Cancelar reserva
if (isset($_POST['cancelar'])) {
    $id_reserva = (int)$_POST['id_reserva'];
    $id_plaza   = (int)$_POST['id_plaza'];

    // Borrar reserva (solo si es del usuario)
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id=? AND id_usuario=?");
    $stmt->bind_param("ii", $id_reserva, $id_usuario);
    $stmt->execute();

    // Liberar plaza
    $conn->query("UPDATE plazas SET estado='libre' WHERE id=$id_plaza");
}

// Listar reservas del usuario (SIN horas)
$sql = "
SELECT r.id AS id_reserva, r.fecha_reserva, p.id AS id_plaza, p.tipo, p.reservado_para
FROM reservas r
JOIN plazas p ON p.id = r.id_plaza
WHERE r.id_usuario = $id_usuario
ORDER BY r.id DESC
";
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

    <?php if ($reservas && $reservas->num_rows > 0): ?>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID Reserva</th>
                    <th>Tipo Plaza</th>
                    <th>Reservado Para</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>

                <?php while ($r = $reservas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $r['id_reserva'] ?></td>
                        <td><?= ucfirst($r['tipo']) ?></td>
                        <td><?= ucfirst(str_replace('_',' ', $r['reservado_para'])) ?></td>
                        <td><?= $r['fecha_reserva'] ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                                <input type="hidden" name="id_plaza" value="<?= $r['id_plaza'] ?>">
                                <button type="submit" name="cancelar" style="background:#e74c3c;">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    <?php else: ?>
        <p>No tienes reservas activas.</p>
    <?php endif; ?>

    <br>
    <a href="panel.php"><button>Volver al Panel</button></a>
</div>

</body>
</html>

