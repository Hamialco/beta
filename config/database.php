<?php
// Configuración de la base de datos
$host = '162.241.62.202'; // Tu host actual
$dbname = 'hadasami_mml';
$username = 'hadasami_api'; // Cambia por tu usuario de BD
$password = 'MazLince'; // Cambia por tu contraseña de BD

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>