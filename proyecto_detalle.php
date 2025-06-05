<?php
require_once 'includes/session_config.php';
require_once 'config/database.php';

// Verificar que el usuario esté logueado
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Obtener ID del proyecto desde la URL
$proyecto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($proyecto_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Obtener información del proyecto
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre as usuario_nombre, u.descripcion as usuario_descripcion, 
           u.foto_perfil as usuario_foto, c.nombre as categoria_nombre,
           (SELECT AVG(estrellas) FROM resenas WHERE proyecto_id = p.id) as promedio_calificacion,
           (SELECT COUNT(*) FROM resenas WHERE proyecto_id = p.id) as total_resenas
    FROM proyectos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.id = ? AND p.estado = 'activo'
");
$stmt->execute([$proyecto_id]);
$proyecto = $stmt->fetch();

if (!$proyecto) {
    header('Location: dashboard.php');
    exit;
}

// Obtener reseñas del proyecto
$stmt = $pdo->prepare("
    SELECT r.*, u.nombre as usuario_nombre, u.foto_perfil as usuario_foto
    FROM resenas r
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE r.proyecto_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$proyecto_id]);
$resenas = $stmt->fetchAll();

// Decodificar imágenes adicionales
$imagenes_adicionales = json_decode($proyecto['imagenes_adicionales'], true) ?: [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($proyecto['titulo']); ?> - Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <link href="assets/styles.css" rel="stylesheet">
    <style>
        .project-hero {
            height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
            margin-bottom: 2rem;
        }
        
        .project-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .project-description {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 3rem;
            color: #555;
        }
        
        .contact-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .contact-btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .whatsapp-btn {
            background-color: #25D366;
            color: white;
        }
        
        .instagram-btn {
            background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D);
            color: white;
        }
        
        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: white;
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: #333;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #1e3c72;
        }
        
        .review-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .review-user-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }
        
        .review-stars {
            color: #FFD700;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .entrepreneur-card {
            display: flex;
            align-items: center;
            background-color: #f9f9f9;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 3rem;
        }
        
        .entrepreneur-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 2rem;
        }
        
        .entrepreneur-info {
            flex: 1;
        }
        
        .entrepreneur-name {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .entrepreneur-btn {
            padding: 0.8rem 1.5rem;
            background-color: #1e3c72;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .entrepreneur-btn:hover {
            background-color: #2a5298;
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .project-hero {
                height: 300px;
            }
            
            .entrepreneur-card {
                flex-direction: column;
                text-align: center;
            }
            
            .entrepreneur-img {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .contact-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Imagen principal del proyecto -->
    <div class="project-hero" style="background-image: url('<?php echo htmlspecialchars($proyecto['imagen_principal']); ?>');"></div>
    
    <div class="container">
        <!-- Título y descripción del proyecto -->
        <h1 class="project-title"><?php echo htmlspecialchars($proyecto['titulo']); ?></h1>
        <p class="project-description"><?php echo nl2br(htmlspecialchars($proyecto['descripcion'])); ?></p>
        
        <!-- Swiper de imágenes adicionales -->
        <?php if (!empty($imagenes_adicionales)): ?>
        <div class="mb-5">
            <h2 class="section-title">Galería del Proyecto</h2>
            <div class="swiper-container" id="gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($imagenes_adicionales as $imagen): ?>
                    <div class="swiper-slide">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" class="img-fluid rounded" alt="Imagen adicional del proyecto">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Botones de contacto -->
        <div class="contact-buttons">
            <?php if ($proyecto['contacto_whatsapp']): ?>
            <a href="https://wa.me/<?php echo htmlspecialchars($proyecto['contacto_whatsapp']); ?>" 
               class="contact-btn whatsapp-btn" target="_blank">
                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
            </a>
            <?php endif; ?>
            
            <?php if ($proyecto['contacto_instagram']): ?>
            <a href="https://instagram.com/<?php echo htmlspecialchars($proyecto['contacto_instagram']); ?>" 
               class="contact-btn instagram-btn" target="_blank">
                <i class="fab fa-instagram"></i> Ver en Instagram
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Sección de reseñas -->
        <div class="mb-5">
            <h2 class="section-title">Reseñas</h2>
            
            <?php if ($proyecto['total_resenas'] > 0): ?>
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        <div class="text-warning" style="font-size: 2rem;">
                            <?php echo number_format($proyecto['promedio_calificacion'], 1); ?> ★
                        </div>
                    </div>
                    <div>
                        <div>Basado en <?php echo $proyecto['total_resenas']; ?> reseña(s)</div>
                    </div>
                </div>
                
                <?php foreach ($resenas as $resena): ?>
                <div class="review-card">
                    <div class="review-header">
                        <?php if ($resena['usuario_foto']): ?>
                        <img src="<?php echo htmlspecialchars($resena['usuario_foto']); ?>" class="review-user-img" alt="<?php echo htmlspecialchars($resena['usuario_nombre']); ?>">
                        <?php endif; ?>
                        <div>
                            <h5><?php echo htmlspecialchars($resena['usuario_nombre']); ?></h5>
                            <div class="review-stars">
                                <?php echo str_repeat('★', $resena['estrellas']) . str_repeat('☆', 5 - $resena['estrellas']); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($resena['comentario']): ?>
                    <p><?php echo htmlspecialchars($resena['comentario']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Este proyecto aún no tiene reseñas.</p>
            <?php endif; ?>
        </div>
        
        <!-- Sección del emprendedor -->
        <div class="mb-5">
            <h2 class="section-title">Sobre el emprendedor</h2>
            <div class="entrepreneur-card">
                <?php if ($proyecto['usuario_foto']): ?>
                <img src="<?php echo htmlspecialchars($proyecto['usuario_foto']); ?>" class="entrepreneur-img" alt="<?php echo htmlspecialchars($proyecto['usuario_nombre']); ?>">
                <?php endif; ?>
                <div class="entrepreneur-info">
                    <h3 class="entrepreneur-name"><?php echo htmlspecialchars($proyecto['usuario_nombre']); ?></h3>
                    <?php if ($proyecto['usuario_descripcion']): ?>
                    <p class="mb-4"><?php echo nl2br(htmlspecialchars($proyecto['usuario_descripcion'])); ?></p>
                    <?php endif; ?>
                    <a href="perfil_emprendedor.php?id=<?php echo $proyecto['usuario_id']; ?>" class="entrepreneur-btn">
                        Ver perfil completo
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        // Inicializar swiper de galería
        <?php if (!empty($imagenes_adicionales)): ?>
        new Swiper('#gallery-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            pagination: {
                el: '#gallery-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                }
            }
        });
        <?php endif; ?>
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>