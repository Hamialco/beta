<?php
// Incluir configuración de sesión centralizada
require_once 'includes/session_config.php';
require_once 'config/database.php';
// Verificar que el usuario esté logueado
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = get_user_id();
$user_role = get_user_role();

// Si es emprendedor, mostrar la versión original
if ($user_role === 'emprendedor') {
    // Obtener información del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch();

    // Obtener proyectos del emprendedor
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre,
               (SELECT AVG(estrellas) FROM resenas WHERE proyecto_id = p.id) as promedio_calificacion,
               (SELECT COUNT(*) FROM resenas WHERE proyecto_id = p.id) as total_resenas
        FROM proyectos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.usuario_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $mis_proyectos = $stmt->fetchAll();
    
    // Obtener categorías para formularios
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY tipo, nombre");
    $categorias = $stmt->fetchAll();
    
    // Incluir la versión original del dashboard para emprendedores
    include 'dashboard_emprendedor.php';
    exit;
}

// Para estudiantes: nueva versión tipo index
// Obtener publicidad activa
$stmt = $pdo->prepare("SELECT * FROM publicidad WHERE estado = 'activo' ORDER BY RAND() LIMIT 1");
$stmt->execute();
$publicidad = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener proyectos activos con sus imágenes
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre as usuario_nombre, c.nombre as categoria_nombre 
    FROM proyectos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.estado = 'activo' 
    ORDER BY RAND()
");
$stmt->execute();
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dividir proyectos en grupos de 4 para los swipers
$proyectos_chunks = array_chunk($proyectos, 4);

// Obtener información del usuario para mostrar en header si es necesario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <link href="assets/styles.css" rel="stylesheet">
    <style>
        .search-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
        }
        
        .search-buttons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .search-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .search-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }
        
        .search-btn i {
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .search-buttons {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            .search-btn {
                width: 80%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Sección de búsqueda -->
    <section class="search-section">
        <div class="container">
            <h2 class="mb-4">¿Estás buscando algo?</h2>
            <div class="search-buttons">
                <a href="productos.php" class="search-btn">
                    <span>🎁</span>
                    Productos
                </a>
                <a href="servicios.php" class="search-btn">
                    <span>🤝</span>
                    Servicios
                </a>
            </div>
        </div>
    </section>

    <!-- Publicidad Banner -->
    <?php if ($publicidad): ?>
    <section class="publicidad-banner">
        <img src="<?php echo htmlspecialchars($publicidad['imagen']); ?>" 
             alt="<?php echo htmlspecialchars($publicidad['titulo']); ?>">
    </section>
    <?php endif; ?>

    <!-- Projects Gallery -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Galería de Proyectos</h2>
            
            <?php if (empty($proyectos)): ?>
                <div class="text-center">
                    <p class="lead">No hay proyectos disponibles en este momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($proyectos_chunks as $index => $chunk): ?>
                    <div class="swiper-container" id="swiper-<?php echo $index; ?>">
                        <div class="swiper-wrapper">
                            <?php foreach ($chunk as $proyecto): ?>
                                <div class="swiper-slide">
    <a href="proyecto_detalle.php?id=<?php echo $proyecto['id']; ?>">
        <div class="swiper-project-slide" 
             style="background-image: url('<?php echo htmlspecialchars($proyecto['imagen_principal']); ?>');">
        </div>
    </a>
</div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Seccion de registro modificada -->
    <section class="register-section">
        <div class="container text-center">
            <h2 class="mb-4">¿Tienes un emprendimiento?</h2>
            <p class="lead mb-4">Cuentanos sobre el para poder ayudarte a que toda la universidad se entere de tu proyecto y logres tus metas más facil y rapido</p>
            <a href="solicitud.php" class="cta-btn">Enviar mi solicitud</a>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Función para cerrar sesión al cerrar ventana
        function setupSessionCleanup() {
            // Detectar cierre de ventana/pestaña
            window.addEventListener('beforeunload', function(e) {
                // Usar sendBeacon para enviar petición de limpieza de sesión
                if (navigator.sendBeacon) {
                    navigator.sendBeacon('includes/cleanup_session.php');
                } else {
                    // Fallback para navegadores antiguos
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'includes/cleanup_session.php', false);
                    xhr.send();
                }
            });

            // Detectar cambio de página (navegación interna)
            window.addEventListener('pagehide', function(e) {
                if (e.persisted) return; // No hacer nada si la página va al cache
                
                if (navigator.sendBeacon) {
                    navigator.sendBeacon('includes/cleanup_session.php');
                }
            });
        }

        // Inicializar limpieza de sesión
        setupSessionCleanup();

        // Initialize Swipers
        <?php foreach ($proyectos_chunks as $index => $chunk): ?>
        new Swiper('#swiper-<?php echo $index; ?>', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            centeredSlides: false,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '#swiper-<?php echo $index; ?> .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                576: {
                    slidesPerView: 1,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                }
            }
        });
        <?php endforeach; ?>
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>