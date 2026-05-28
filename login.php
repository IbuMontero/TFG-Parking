<?php
include("includes/db.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Evitar inyección básica
    $email = $conn->real_escape_string($email);

    $sql = "SELECT * FROM usuarios WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Comprobamos el hash
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: panel.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Parking</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">

    <h2>Acceso al Parking</h2>

    <form method="POST">

        <input type="email" name="email" placeholder="Correo" required>

        <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Entrar</button>

    </form>

</div>

</body>
