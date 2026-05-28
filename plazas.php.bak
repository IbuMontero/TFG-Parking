<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit();
}
include("includes/db.php");

// --- Actualizar estado ---
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $sql = "UPDATE plazas SET estado='$estado' WHERE id=$id";
    $conn->query($sql);
}

// --- Agregar plaza ---
if(isset($_POST['add'])){
    $tipo = $_POST['tipo'];
    $reservado_para = $_POST['reservado_para'];
    $sql = "INSERT INTO plazas (tipo, reservado_para) VALUES ('$tipo', '$reservado_para')";
    $conn->query($sql);
}

// --- Eliminar plaza ---
if(isset($_POST['delete'])){
    $id = $_POST['id'];
    $sql = "DELETE FROM plazas WHERE id=$id";
    $conn->query($sql);
}

$result = $conn->query("SELECT * FROM plazas ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Plazas</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-container">
    <h2>Gestión de Plazas</h2>

    <form method="POST" action="">
        <select name="tipo" required>
            <option value="coche">Coche</option>
            <option value="moto">Moto</option>
            <option value="bici">Bici</option>
        </select>
        <select name="reservado_para" required>
            <option value="ninguno">General</option>
            <option value="profesorado">Profesorado</option>
            <option value="alumnado">Alumnado</option>
            <option value="movilidad_reducida">Movilidad Reducida</option>
        </select>
        <button type="submit" name="add">Agregar Plaza</button>
    </form>

    <table border="1" style="width:100%; margin-top:15px; border-collapse: collapse;">
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Reservado Para</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= ucfirst($row['tipo']) ?></td>
            <td><?= ucfirst(str_replace('_',' ',$row['reservado_para'])) ?></td>
            <td><?= ucfirst($row['estado']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <select name="estado">
                        <option value="libre">Libre</option>
                        <option value="ocupada">Ocupada</option>
                        <option value="reservada">Reservada</option>
                    </select>
                    <button type="submit" name="update">Actualizar</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete" style="background:red;">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="panel.php"><button>Volver al Panel</button></a>
</div>
</body>
</html>
