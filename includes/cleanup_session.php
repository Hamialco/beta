<?php
// cleanup_session.php - Archivo para limpiar sesiones mejorado
require_once 'session_config.php';

// Función para log de depuración (opcional)
function debug_log($message) {
    // Descomenta la siguiente línea si quieres logs para depuración
    // error_log(date('Y-m-d H:i:s') . " - Session Cleanup: " . $message . "\n", 3, "../logs/session_cleanup.log");
}

// Verificar si hay una sesión activa antes de destruirla
if (session_status() === PHP_SESSION_ACTIVE) {
    $session_id = session_id();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'unknown';
    
    debug_log("Destroying session $session_id for user $user_id");
    
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Eliminar la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir la sesión
    session_destroy();
    
    debug_log("Session destroyed successfully");
} else {
    debug_log("No active session found");
}

// Manejar diferentes tipos de respuesta
$request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$is_beacon = isset($_SERVER['HTTP_USER_AGENT']) && 
             (strpos($_SERVER['HTTP_USER_AGENT'], 'Beacon') !== false ||
              $request_method === 'POST');

// Si se solicita redirección específica
if (isset($_GET['redirect']) && $_GET['redirect'] === 'true') {
    debug_log("Redirecting to index.php");
    header('Location: ../index.php');
    exit;
}

// Para solicitudes de sendBeacon o AJAX
if ($is_beacon || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    // Establecer headers apropiados para CORS si es necesario
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // Responder con éxito
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Session cleaned']);
    debug_log("Responded to beacon/ajax request");
} else {
    // Para solicitudes normales, responder con texto simple
    http_response_code(200);
    echo 'OK';
    debug_log("Responded to normal request");
}
?>