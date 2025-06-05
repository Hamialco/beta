<?php
require_once 'includes/session_config.php';
require_once 'config/database.php';

// Verificar que el usuario esté logueado
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Obtener ID del emprendedor desde la URL
$emprendedor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($emprendedor_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Obtener información del emprendedor
$stmt = $pdo->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM proyectos WHERE usuario_id = u.id AND estado = 'activo') as total_proyectos
    FROM usuarios u 
    WHERE u.id = ? AND u.rol = 'emprendedor'
");
$stmt->execute([$emprendedor_id]);
$emprendedor = $stmt->fetch();

if (!$emprendedor) {
    header('Location: dashboard.php');
    exit;
}

// Obtener proyectos del emprendedor
$stmt = $pdo->prepare("
    SELECT p.*, c.nombre as categoria_nombre,
           (SELECT AVG(estrellas) FROM resenas WHERE proyecto_id = p.id) as promedio_calificacion,
           (SELECT COUNT(*) FROM resenas WHERE proyecto_id = p.id) as total_resenas
    FROM proyectos p 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.usuario_id = ? AND p.estado = 'activo'
    ORDER BY p.created_at DESC
");
$stmt->execute([$emprendedor_id]);
$proyectos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($emprendedor['nombre']); ?> - Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <link href="assets/styles.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            margin-bottom: 1.5rem;
        }
        
        .profile-name {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .profile-meta {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .profile-bio {
            max-width: 800px;
            margin: 0 auto 2rem;
            font-size: 1.1rem;
            line-height: 1.8;
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
        
        .project-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .project-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .project-body {
            padding: 1.5rem;
        }
        
        .project-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        
        .project-category {
            display: inline-block;
            background-color: #f0f0f0;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        .project-description {
            color: #666;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .project-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #FFD700;
            margin-bottom: 1rem;
        }
        
        .project-link {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #1e3c72;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .project-link:hover {
            background-color: #2a5298;
            color: white;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background-color: #1e3c72;
            color: white;
        }
        
        @media (max-width: 768px) {
            .profile-name {
                font-size: 2rem;
            }
            
            .profile-bio {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Encabezado del perfil -->
    <section class="profile-header text-center">
        <div class="container">
            <?php if ($emprendedor['foto_perfil']): ?>
            <img src="<?php echo htmlspecialchars($emprendedor['foto_perfil']); ?>" class="profile-img" alt="<?php echo htmlspecialchars($emprendedor['nombre']); ?>">
            <?php endif; ?>
            <h1 class="profile-name"><?php echo htmlspecialchars($emprendedor['nombre']); ?></h1>
            
            <div class="profile-meta">
                <?php if ($emprendedor['carrera']): ?>
                <div class="profile-meta-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span><?php echo htmlspecialchars($emprendedor['carrera']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($emprendedor['semestre']): ?>
                <div class="profile-meta-item">
                    <i class="fas fa-layer-group"></i>
                    <span>Semestre <?php echo $emprendedor['semestre']; ?></span>
                </div>
                <?php endif; ?>
                
                <div class="profile-meta-item">
                    <i class="fas fa-project-diagram"></i>
                    <span><?php echo $emprendedor['total_proyectos']; ?> proyecto(s)</span>
                </div>
            </div>
            
            <?php if ($emprendedor['descripcion']): ?>
            <div class="profile-bio">
                <?php echo nl2br(htmlspecialchars($emprendedor['descripcion'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($emprendedor['motivacion']): ?>
            <div class="profile-bio">
                <strong>Motivación:</strong> <?php echo nl2br(htmlspecialchars($emprendedor['motivacion'])); ?>
            </div>
            <?php endif; ?>
            
            <div class="social-links">
                <?php if ($emprendedor['telefono']): ?>
                <a href="https://wa.me/<?php echo htmlspecialchars($emprendedor['telefono']); ?>" class="social-link" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <?php endif; ?>
                
                <?php if ($emprendedor['instagram']): ?>
                <a href="https://instagram.com/<?php echo htmlspecialchars($emprendedor['instagram']); ?>" class="social-link" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Proyectos del emprendedor -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Proyectos</h2>
            
            <?php if (empty($proyectos)): ?>
                <div class="text-center">
                    <p class="lead">Este emprendedor no tiene proyectos activos.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($proyectos as $proyecto): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="project-card">
                            <img src="<?php echo htmlspecialchars($proyecto['imagen_principal']); ?>" class="project-img" alt="<?php echo htmlspecialchars($proyecto['titulo']); ?>">
                            <div class="project-body">
                                <h3 class="project-title"><?php echo htmlspecialchars($proyecto['titulo']); ?></h3>
                                <span class="project-category"><?php echo htmlspecialchars($proyecto['categoria_nombre']); ?></span>
                                <p class="project-description"><?php echo htmlspecialchars(substr($proyecto['descripcion'], 0, 150)); ?>...</p>
                                
                                <?php if ($proyecto['total_resenas'] > 0): ?>
                                <div class="project-rating">
                                    <span><?php echo number_format($proyecto['promedio_calificacion'], 1); ?></span>
                                    <span>★</span>
                                    <span>(<?php echo $proyecto['total_resenas']; ?>)</span>
                                </div>
                                <?php endif; ?>
                                
                                <a href="proyecto_detalle.php?id=<?php echo $proyecto['id']; ?>" class="project-link">Ver proyecto</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>