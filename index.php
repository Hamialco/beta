<?php
session_start();
require_once 'config/database.php';

// Obtener publicidad activa
$stmt = $pdo->prepare("SELECT * FROM publicidad WHERE estado = 'activo' AND (fecha_activacion IS NULL OR fecha_activacion <= NOW()) AND fecha_desactivacion > NOW() ORDER BY RAND() LIMIT 1");
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
    <title>Galería de Emprendimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
        <link href="assets/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🖼️ Galería Emprendedores</a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="navbar-text me-3">Hola, <?php echo htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['matricula']); ?></span>
                    <?php if ($_SESSION['rol'] === 'emprendedor'): ?>
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    <a class="nav-link" href="register.php">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Descubre Emprendimientos Estudiantiles</h1>
            <p class="lead">Una plataforma donde los estudiantes muestran sus proyectos y servicios</p>
        </div>
    </section>

    <!-- Publicidad Section -->
    <?php if ($publicidad): ?>
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Eventos y Anuncios</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="publicidad-card">
                        <img src="<?php echo htmlspecialchars($publicidad['imagen']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($publicidad['titulo']); ?>"
                             style="height: 300px; object-fit: cover;">
                        <div class="card-body bg-dark text-white">
                            <h5 class="card-title"><?php echo htmlspecialchars($publicidad['titulo']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($publicidad['propietario']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                    <div class="swiper-container mb-4" id="swiper-<?php echo $index; ?>">
                        <div class="swiper-wrapper">
                            <?php 
                            // Duplicar slides para efecto infinito
                            $slides = array_merge($chunk, $chunk, $chunk);
                            foreach ($slides as $proyecto): 
                            ?>
                                <div class="swiper-slide">
                                    <div class="project-slide position-relative" 
                                         style="background-image: url('<?php echo htmlspecialchars($proyecto['imagen_principal']); ?>');"
                                         onclick="<?php echo isset($_SESSION['user_id']) ? "showProjectModal(" . $proyecto['id'] . ")" : "showLoginRequired()"; ?>">
                                        
                                        <?php if (!isset($_SESSION['user_id'])): ?>
                                            <div class="blur-overlay position-absolute w-100 h-100"></div>
                                            <div class="login-required">
                                                <h6>¡Regístrate para ver más detalles!</h6>
                                                <a href="register.php" class="btn btn-primary btn-sm mt-2">Registrarse</a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="project-overlay">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($proyecto['titulo']); ?></h6>
                                            <small><?php echo htmlspecialchars($proyecto['categoria_nombre']); ?></small>
                                        </div>
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

    <!-- Register CTA -->
    <section class="register-section">
        <div class="container text-center">
            <h2 class="mb-4">¡Únete a Nuestra Comunidad!</h2>
            <p class="lead mb-4">Regístrate para explorar todos los proyectos y conectar con emprendedores</p>
            <a href="register.php" class="cta-btn">Registrarse Ahora</a>
        </div>
    </section>

    <!-- Entrepreneur CTA -->
    <section class="entrepreneur-section">
        <div class="container text-center">
            <h2 class="mb-4">¿Tienes un Emprendimiento?</h2>
            <p class="lead mb-4">Solicita convertirte en emprendedor y comparte tus proyectos con la comunidad</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="solicitud-emprendedor.php" class="cta-btn">Enviar Solicitud</a>
            <?php else: ?>
                <a href="register.php" class="cta-btn">Regístrate Primero</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal para detalles del proyecto -->
    <div class="modal fade" id="projectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="projectModalBody">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swipers
        <?php foreach ($proyectos_chunks as $index => $chunk): ?>
        new Swiper('#swiper-<?php echo $index; ?>', {
            slidesPerView: 'auto',
            spaceBetween: 20,
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
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                }
            }
        });
        <?php endforeach; ?>

        function showProjectModal(projectId) {
            fetch(`get-project-details.php?id=${projectId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('projectModalTitle').textContent = data.titulo;
                    document.getElementById('projectModalBody').innerHTML = data.html;
                    new bootstrap.Modal(document.getElementById('projectModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles del proyecto');
                });
        }

        function showLoginRequired() {
            alert('¡Regístrate para ver más detalles de los proyectos!');
            window.location.href = 'register.php';
        }
    </script>
        <?php include 'includes/footer.php'; ?>
</body>
</html>