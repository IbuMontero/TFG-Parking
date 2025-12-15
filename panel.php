<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-container">
    <h2>Bienvenido al Sistema de Parking</h2>
    <p>Tu rol: <b><?php echo ucfirst($rol); ?></b></p>

    <?php if($rol == 'profesorado' || $rol == 'alumnado'): ?>
    <a href="reservar.php"><button>Reservar Plaza</button></a>
    <?php endif ?>

    <?php if($rol == 'profesorado' || $rol == 'alumnado'): ?>
    <a href="mis_reservas.php"><button>Mis Reservas</button></a>
    <?php endif ?>

    <?php if($rol == 'admin'): ?>
        <a href="plazas.php"><button>Gestionar Plazas</button></a>
    <?php endif ?>

    <a href="logout.php"><button>Salir</button></a>
</div>
</body>
</html>

