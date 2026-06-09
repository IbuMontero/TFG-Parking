<?php
include("includes/db.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Configuración LDAP
    $ldap_host = "ldap://127.0.0.1";
    $ldap_base_dn = "dc=parking,dc=local";

    // Conectar con LDAP
    $ldap_conn = ldap_connect($ldap_host);

    if (!$ldap_conn) {
        $error = "No se pudo conectar con el servidor LDAP.";
    } else {

        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

        // Buscar usuario LDAP por email
        $safe_email = ldap_escape($email, "", LDAP_ESCAPE_FILTER);
        $filter = "(mail=$safe_email)";

        $search = ldap_search($ldap_conn, $ldap_base_dn, $filter);

        if ($search) {
            $entries = ldap_get_entries($ldap_conn, $search);

            if ($entries["count"] > 0) {

                $user_dn = $entries[0]["dn"];

                // Validar contraseña contra LDAP
                if (@ldap_bind($ldap_conn, $user_dn, $password)) {

                    // Si LDAP valida, buscamos el usuario en MySQL para obtener su rol
                    $email_sql = $conn->real_escape_string($email);
                    $sql = "SELECT * FROM usuarios WHERE email='$email_sql' LIMIT 1";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        $user = $result->fetch_assoc();

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['rol'] = $user['rol'];

                        header("Location: panel.php");
                        exit();
                    } else {
                        $error = "Usuario validado en LDAP, pero no existe en la base de datos.";
                    }

                } else {
                    $error = "Contraseña incorrecta.";
                }

            } else {
                $error = "Usuario no encontrado en LDAP.";
            }

        } else {
            $error = "Error al buscar el usuario en LDAP.";
        }

        ldap_close($ldap_conn);
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

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>

	<?php if(isset($error)): ?>
    		<div class="error-message">
        		<?php echo htmlspecialchars($error); ?>
    		</div>
        <?php endif; ?>
</div>

</body>
</html>
