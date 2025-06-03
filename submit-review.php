<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$project_id = $input['project_id'] ?? 0;
$rating = intval($input['rating'] ?? 0);
$comment = trim($input['comment'] ?? '');

// Validaciones
if (!$project_id || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

if (strlen($comment) > 100) {
    echo json_encode(['success' => false, 'error' => 'Comentario muy largo']);
    exit;
}

try {
    // Verificar que el proyecto existe y está activo
    $stmt = $pdo->prepare("SELECT usuario_id FROM proyectos WHERE id = ? AND estado = 'activo'");
    $stmt->execute([$project_id]);
    $proyecto = $stmt->fetch();
    
    if (!$proyecto) {
        echo json_encode(['success' => false, 'error' => 'Proyecto no encontrado']);
        exit;
    }
    
    // Verificar que no es el propietario del proyecto
    if ($proyecto['usuario_id'] == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => 'No puedes reseñar tu propio proyecto']);
        exit;
    }
    
    // Insertar o actualizar reseña (debido a la restricción UNIQUE)
    $stmt = $pdo->prepare("
        INSERT INTO resenas (proyecto_id, usuario_id, estrellas, comentario) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        estrellas = VALUES(estrellas), 
        comentario = VALUES(comentario)
    ");
    
    $stmt->execute([$project_id, $_SESSION['user_id'], $rating, $comment ?: null]);
    
    echo json_encode(['success' => true, 'message' => 'Reseña guardada exitosamente']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>