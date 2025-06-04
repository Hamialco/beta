<?php
// session_config.php - Archivo para manejar sesiones de forma centralizada

// Configurar parámetros de sesión ANTES de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    // Configurar sesión para que se cierre al cerrar el navegador
    ini_set('session.cookie_lifetime', 0); // 0 = se cierra al cerrar navegador
    ini_set('session.gc_maxlifetime', 86400); // 24 horas máximo en servidor
    ini_set('session.cookie_httponly', true); // Mayor seguridad
    ini_set('session.cookie_secure', false); // Cambiar a true si usas HTTPS
    
    // Iniciar sesión después de configurar los parámetros
    session_start();
}

// Función para verificar si el usuario está logueado
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
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

// Función para cerrar sesión (aunque no la usaremos en menús)
function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>