<?php
session_start();
require_once 'config/database.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$project_id = $_GET['id'] ?? 0;

if (!$project_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de proyecto requerido']);
    exit;
}

// Obtener detalles del proyecto
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre as usuario_nombre, u.instagram, u.telefono, 
           c.nombre as categoria_nombre, c.tipo as categoria_tipo
    FROM proyectos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.id = ? AND p.estado = 'activo'
");
$stmt->execute([$project_id]);
$proyecto = $stmt->fetch();

if (!$proyecto) {
    http_response_code(404);
    echo json_encode(['error' => 'Proyecto no encontrado']);
    exit;
}

// Obtener promedio de calificaciones
$stmt = $pdo->prepare("
    SELECT AVG(estrellas) as promedio, COUNT(*) as total_resenas 
    FROM resenas 
    WHERE proyecto_id = ?
");
$stmt->execute([$project_id]);
$calificacion = $stmt->fetch();

// Procesar imágenes adicionales
$imagenes_adicionales = [];
if ($proyecto['imagenes_adicionales']) {
    $imagenes_adicionales = json_decode($proyecto['imagenes_adicionales'], true) ?: [];
}

// Construir HTML
$html = '
<div class="row">
    <div class="col-md-6">
        <img src="' . htmlspecialchars($proyecto['imagen_principal']) . '" 
             class="img-fluid rounded mb-3" alt="' . htmlspecialchars($proyecto['titulo']) . '">';

if (count($imagenes_adicionales) > 0) {
    $html .= '<div class="row">';
    foreach ($imagenes_adicionales as $img) {
        $html .= '<div class="col-4 mb-2">
                    <img src="' . htmlspecialchars($img) . '" class="img-fluid rounded" 
                         style="height: 80px; object-fit: cover; cursor: pointer;"
                         onclick="document.querySelector(\'.modal img\').src = this.src">
                  </div>';
    }
    $html .= '</div>';
}

$html .= '</div>
    <div class="col-md-6">
        <div class="mb-3">
            <span class="badge bg-primary mb-2">' . htmlspecialchars($proyecto['categoria_nombre']) . '</span>
            <span class="badge bg-secondary mb-2">' . ucfirst($proyecto['categoria_tipo']) . '</span>
        </div>
        
        <h4>' . htmlspecialchars($proyecto['titulo']) . '</h4>
        <p class="text-muted">Por: ' . htmlspecialchars($proyecto['usuario_nombre'] ?: 'Usuario') . '</p>
        
        <div class="mb-3">
            <strong>Descripción:</strong>
            <p>' . nl2br(htmlspecialchars($proyecto['descripcion'])) . '</p>
        </div>';

// Calificación
if ($calificacion['total_resenas'] > 0) {
    $html .= '<div class="mb-3">
                <strong>Calificación:</strong>
                <div class="d-flex align-items-center">
                    <div class="me-2">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= round($calificacion['promedio']) ? '⭐' : '☆';
    }
    $html .= '</div>
                    <span>(' . number_format($calificacion['promedio'], 1) . '/5 - ' . $calificacion['total_resenas'] . ' reseñas)</span>
                </div>
              </div>';
}

// Información de contacto
$html .= '<div class="mb-3">
            <strong>Contacto:</strong>';
if ($proyecto['contacto_whatsapp']) {
    $html .= '<div><a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $proyecto['contacto_whatsapp']) . '" 
                     target="_blank" class="btn btn-success btn-sm me-2">
                    📱 WhatsApp
             </a></div>';
}
if ($proyecto['contacto_instagram']) {
    $html .= '<div class="mt-2"><a href="https://instagram.com/' . htmlspecialchars($proyecto['contacto_instagram']) . '" 
                     target="_blank" class="btn btn-outline-primary btn-sm">
                    📷 @' . htmlspecialchars($proyecto['contacto_instagram']) . '
             </a></div>';
}
$html .= '</div>';

// Reseña si no es el dueño del proyecto
if ($_SESSION['user_id'] != $proyecto['usuario_id']) {
    $html .= '<div class="mt-4">
                <h6>Calificar este proyecto:</h6>
                <form id="reviewForm" onsubmit="submitReview(event, ' . $project_id . ')">
                    <div class="mb-2">
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5"><label for="star5">⭐</label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4">⭐</label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3">⭐</label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2">⭐</label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1">⭐</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" placeholder="Comentario opcional (máx. 100 caracteres)" 
                                  maxlength="100" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Enviar Reseña</button>
                </form>
              </div>';
}

$html .= '</div>
</div>

<style>
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.star-rating input[type="radio"] {
    display: none;
}
.star-rating label {
    cursor: pointer;
    font-size: 20px;
    color: #ddd;
    transition: color 0.2s;
}
.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type="radio"]:checked ~ label {
    color: #ffd700;
}
</style>

<script>
function submitReview(event, projectId) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const rating = formData.get("rating");
    const comment = formData.get("comment");
    
    if (!rating) {
        alert("Por favor selecciona una calificación");
        return;
    }
    
    fetch("submit-review.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            project_id: projectId,
            rating: rating,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("¡Reseña enviada exitosamente!");
            location.reload();
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error al enviar la reseña");
    });
}
</script>';

echo json_encode([
    'titulo' => $proyecto['titulo'],
    'html' => $html
]);
?>
