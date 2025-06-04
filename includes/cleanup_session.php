<?php
// cleanup_session.php - Archivo para limpiar sesiones
require_once 'session_config.php';

// Destruir la sesión
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Si se solicita redirección (cuando se detecta inactividad)
if (isset($_GET['redirect']) && $_GET['redirect'] === 'true') {
    header('Location: ../index.php');
    exit;
}

// Para solicitudes AJAX/Beacon, solo responder con éxito
http_response_code(200);
echo 'OK';
?>