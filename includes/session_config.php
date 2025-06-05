<?php
// session_config.php - Versión corregida y simplificada

// Solo iniciar sesión si no hay una activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Configurar parámetros de sesión ANTES de iniciarla
    ini_set('session.cookie_lifetime', 0); // Se cierra al cerrar navegador
    ini_set('session.gc_maxlifetime', 86400); // 24 horas máximo en servidor
    ini_set('session.cookie_httponly', true); // Mayor seguridad
    ini_set('session.cookie_secure', false); // Cambiar a true en HTTPS
    ini_set('session.use_only_cookies', true); // Solo usar cookies para sesiones
    
    // Iniciar sesión
    session_start();
}

// Función para verificar si el usuario está logueado
function is_logged_in() {
    // Verificar que la sesión esté activa y que existan los datos necesarios
    return (session_status() === PHP_SESSION_ACTIVE) && 
           isset($_SESSION['user_id']) && 
           !empty($_SESSION['user_id']) &&
           isset($_SESSION['rol']);
}

// Función para obtener el rol del usuario
function get_user_role() {
    return is_logged_in() ? $_SESSION['rol'] : null;
}

// Función para obtener el ID del usuario
function get_user_id() {
    return is_logged_in() ? $_SESSION['user_id'] : null;
}

// Función para obtener información completa del usuario
function get_user_info() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'matricula' => $_SESSION['matricula'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? null,
        'rol' => $_SESSION['rol'] ?? null
    ];
}

// Función para verificar timeout de sesión (opcional)
function check_session_timeout($timeout_minutes = 60) {
    if (!is_logged_in()) {
        return false;
    }
    
    // Si no existe timestamp de última actividad, crearlo
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    // Verificar si ha pasado el tiempo límite
    $inactive_time = time() - $_SESSION['last_activity'];
    if ($inactive_time > ($timeout_minutes * 60)) {
        // Sesión expirada
        session_unset();
        session_destroy();
        return false;
    }
    
    // Actualizar timestamp de actividad
    $_SESSION['last_activity'] = time();
    return true;
}

// Función para cerrar sesión completamente
function logout() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Limpiar variables de sesión
        $_SESSION = array();
        
        // Eliminar cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir sesión
        session_destroy();
    }
}
?>