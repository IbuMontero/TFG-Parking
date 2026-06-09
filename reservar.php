<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("includes/db.php");
include("includes/mail.php");

$id_usuario = $_SESSION['user_id'];
$rol = $_SESSION['rol'];
$mensaje = "";

// --- Reservar plaza ---
if (isset($_POST['reservar'])) {
    $id_plaza = $_POST['id_plaza'];

    // Fecha actual
    $fecha = date('Y-m-d');

    // Obtener datos del usuario para el correo
    $stmt_user = $conn->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
    $stmt_user->bind_param("i", $id_usuario);
    $stmt_user->execute();
    $usuario = $stmt_user->get_result()->fetch_assoc();

    // Insertar reserva
    $stmt = $conn->prepare(
        "INSERT INTO reservas (id_usuario, id_plaza, fecha_reserva)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iis", $id_usuario, $id_plaza, $fecha);

    if ($stmt->execute()) {

        // Marcar la plaza como reservada
        $stmt_update = $conn->prepare("UPDATE plazas SET estado='reservada' WHERE id=?");
        $stmt_update->bind_param("i", $id_plaza);
        $stmt_update->execute();

        // Enviar correo de confirmación
        if ($usuario) {
            enviarCorreoReserva($usuario['email'], $usuario['nombre'], $id_plaza, $fecha);
        }

        $mensaje = "Reserva realizada correctamente. Se ha enviado un correo de confirmación.";
    } else {
        $mensaje = "Error al realizar la reserva.";
    }
}

// --- Consultar plazas libres ---
$sql = "SELECT * FROM plazas
        WHERE estado='libre'
        AND (reservado_para='ninguno' OR reservado_para='$rol')";
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

    <?php if ($mensaje != ""): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <?php if ($plazas->num_rows > 0): ?>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Reservado Para</th>
                    <th>Acción</th>
                </tr>

                <?php while ($p = $plazas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= ucfirst($p['tipo']) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $p['reservado_para'])) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id_plaza" value="<?= $p['id'] ?>">
                                <button type="submit" name="reservar">Reservar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    <?php else: ?>
        <p>No hay plazas disponibles en este momento.</p>
    <?php endif; ?>

    <br>
    <a href="panel.php"><button>Volver al Panel</button></a>
</div>

</body>
</html>
