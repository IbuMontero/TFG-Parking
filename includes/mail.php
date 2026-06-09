<?php

function enviarCorreoReserva($destinatario, $nombreUsuario, $idPlaza, $fechaReserva) {
    $asunto = "Confirmación de reserva de parking";

    $mensaje = "Hola $nombreUsuario,\n\n";
    $mensaje .= "Tu reserva se ha realizado correctamente.\n\n";
    $mensaje .= "Datos de la reserva:\n";
    $mensaje .= "- Plaza: $idPlaza\n";
    $mensaje .= "- Fecha: $fechaReserva\n\n";
    $mensaje .= "Sistema de Gestión de Parking\n";

    $cabeceras = "From: parking@parking.local\r\n";

    return mail($destinatario, $asunto, $mensaje, $cabeceras);
}

?>
