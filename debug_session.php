<?php
// debug_session.php - Crear este archivo en la raíz del proyecto para diagnosticar
require_once 'includes/session_config.php';

echo "<h2>Diagnóstico de Sesión</h2>";
echo "<strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "<br><br>";

// 1. Estado de la sesión
echo "<strong>1. Estado de PHP Session:</strong><br>";
echo "Session Status: " . session_status() . " (1=disabled, 2=none, 3=active)<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Name: " . session_name() . "<br><br>";

// 2. Configuración de sesión
echo "<strong>2. Configuración de Sesión:</strong><br>";
echo "cookie_lifetime: " . ini_get('session.cookie_lifetime') . "<br>";
echo "gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "<br>";
echo "cookie_httponly: " . ini_get('session.cookie_httponly') . "<br>";
echo "cookie_secure: " . ini_get('session.cookie_secure') . "<br><br>";

// 3. Contenido de $_SESSION
echo "<strong>3. Contenido de \$_SESSION:</strong><br>";
if (empty($_SESSION)) {
    echo "⚠️ \$_SESSION está vacío<br>";
} else {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}
echo "<br>";

// 4. Cookies de sesión
echo "<strong>4. Cookies:</strong><br>";
if (empty($_COOKIE)) {
    echo "⚠️ No hay cookies<br>";
} else {
    foreach ($_COOKIE as $name => $value) {
        if (strpos($name, 'PHPSESSID') !== false || strpos($name, 'sess') !== false) {
            echo "$name: $value<br>";
        }
    }
}
echo "<br>";

// 5. Prueba de funciones
echo "<strong>5. Prueba de Funciones:</strong><br>";
echo "is_logged_in(): " . (is_logged_in() ? '✅ TRUE' : '❌ FALSE') . "<br>";
echo "get_user_role(): " . (get_user_role() ?: 'NULL') . "<br>";
echo "get_user_id(): " . (get_user_id() ?: 'NULL') . "<br><br>";

// 6. Información del servidor
echo "<strong>6. Información del Servidor:</strong><br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br><br>";

// 7. Headers enviados
echo "<strong>7. Headers:</strong><br>";
if (headers_sent($file, $line)) {
    echo "⚠️ Headers ya enviados en $file línea $line<br>";
} else {
    echo "✅ Headers no enviados aún<br>";
}

// 8. Test de escritura en sesión
echo "<br><strong>8. Test de Escritura en Sesión:</strong><br>";
$_SESSION['debug_test'] = 'test_value_' . time();
echo "Valor escrito: " . $_SESSION['debug_test'] . "<br>";

// 9. Links de prueba
echo "<br><strong>9. Links de Prueba:</strong><br>";
echo '<a href="dashboard.php">Ir a Dashboard</a><br>';
echo '<a href="perfil_emprendedor.php?id=1">Ir a Perfil Emprendedor (ID=1)</a><br>';
echo '<a href="login.php">Ir a Login</a><br>';
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
strong { color: #666; }
pre { background: #f4f4f4; padding: 10px; border-radius: 4px; }
a { display: inline-block; margin: 5px; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
a:hover { background: #0056b3; }
</style>