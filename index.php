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
    <style>
        /* Estilos específicos para el index */
        .publicidad-banner {
            width: 100%;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .publicidad-banner img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
        }
        
        .swiper-project-slide {
            position: relative;
            width: 100%;
            aspect-ratio: 1/1; /* Mantiene proporción cuadrada */
            background-size: cover;
            background-position: center;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
            margin: 0 auto;
        }
        
        .swiper-project-slide:hover {
            transform: scale(1.05);
        }
        
        .swiper-container {
            margin: 20px 0;
            padding: 20px 0;
        }
        
        .swiper-pagination {
            position: relative !important;
            margin-top: 20px !important;
        }
        
        .swiper-pagination-bullet {
            background: #1f4f82;
            opacity: 0.5;
        }
        
        .swiper-pagination-bullet-active {
            background: #1f4f82;
            opacity: 1;
        }
        
        /* Estilos corregidos para las secciones */
        .entrepreneur-section .cta-btn {
            background: #f1c40f;
            color: #000;
            border: 2px solid #f1c40f;
        }
        
        .entrepreneur-section .cta-btn:hover {
            background: #f39c12;
            color: #000;
            border-color: #f39c12;
        }
        
        .register-section {
            background: #f1c40f;
            color: #000;
        }
        
        .register-section .cta-btn {
            background: #1f4f82;
            color: white;
            border: 2px solid #1f4f82;
        }
        
        .register-section .cta-btn:hover {
            background: #2c5aa0;
            color: white;
            border-color: #2c5aa0;
        }
        
        @media (max-width: 768px) {
            .swiper-project-slide {
                /* El aspect-ratio se mantiene, no necesitamos altura fija */
            }
        }
        
        @media (max-width: 576px) {
            .swiper-project-slide {
                /* El aspect-ratio se mantiene, no necesitamos altura fija */
            }
        }
    </style>
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