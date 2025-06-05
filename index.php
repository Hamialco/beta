<?php
session_start();
require_once 'config/database.php';

// Obtener publicidad activa (consulta simplificada)
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mural Maz Lince</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <link href="assets/styles.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

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
                                    <div class="swiper-project-slide" 
                                         style="background-image: url('<?php echo htmlspecialchars($proyecto['imagen_principal']); ?>');">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Seccion azul -->
    <section class="entrepreneur-section">
        <div class="container text-center">
            <h2 class="mb-4">¿Tienes un emprendimiento?</h2>
            <p class="lead mb-4">Hagamos que todos se enteren de él.</p>
            <a href="login.php" class="cta-btn">Cuéntanos más</a>
        </div>
    </section>

    <!-- Seccion amarillo -->
    <section class="register-section">
        <div class="container text-center">
            <h2 class="mb-4">¿Ya te registraste?</h2>
            <p class="lead mb-4">Podrás contactar directamente a los emprendedores o volverte uno.</p>
            <a href="register.php" class="cta-btn">Únete</a>
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

        // Inicializar limpieza de sesión solo si hay usuario logueado
        <?php if (isset($_SESSION['user_id'])): ?>
        setupSessionCleanup();
        <?php endif; ?>

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