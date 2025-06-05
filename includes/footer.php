<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer con Estilos Internos</title>
    <style>
        /* Estilos básicos para el body */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Estilos del Footer */
        .footer {
            background: #8B1A29;
            color: white;
            padding: 2rem 1.5rem;
            margin-top: auto;
        }

        .footer-container {
            max-width: 1800px;
            margin: 0 auto;
            width: 100%;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }

        .footer-logo {
            height: 105px;
            transition: height 0.3s ease;
        }

        .footer-social {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1c40f;
            text-decoration: none;
            font-size: 1.8rem;
            transition: all 0.3s ease;
            padding: 0.5rem;
            width: 40px;
            height: 40px;
        }

        .social-icon:hover {
            color: #f39c12;
            transform: translateY(-2px);
        }

        .social-icon img {
            width: 32px !important;
            height: 32px !important;
            object-fit: contain;
        }

        .social-icon svg {
            width: 32px !important;
            height: 32px !important;
        }

        .footer-bottom {
            display: flex;
            align-items: flex-start;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-links li {
            margin: 0;
        }

        .footer-links a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .footer-links a:hover {
            color: #f1c40f;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -3px;
            left: 0;
            background-color: #f1c40f;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        /* Responsive Design para Footer */
        @media (max-width: 768px) {
            .footer-logo {
                height: 80px;
            }

            .footer-social {
                gap: 1rem;
            }

            .social-icon {
                font-size: 1.6rem;
            }

            .footer-links ul {
                gap: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .footer {
                padding: 1.5rem 1rem;
            }

            .footer-top {
                padding-bottom: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .footer-logo {
                height: 60px;
            }

            .social-icon {
                font-size: 1.4rem;
            }

            .footer-links a {
                font-size: 1rem;
            }

            .footer-links ul {
                gap: 0.6rem;
            }
        }

        /* Estilos adicionales para el contenido de ejemplo */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
            background: #f8f9fa;
        }

        .content-wrapper {
            text-align: center;
            max-width: 800px;
        }

        .content-wrapper h1 {
            color: #333;
            margin-bottom: 1rem;
        }

        .content-wrapper p {
            color: #666;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Contenido principal de ejemplo -->
    <div class="main-content">
        <div class="content-wrapper">
            <h1>Página de Ejemplo</h1>
            <p>Este es un ejemplo de página con el footer integrado y estilos internos.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Nivel Superior: Logo e Iconos -->
            <div class="footer-top">
                <img src="assets/logo.jpg" alt="Logo" class="footer-logo">
                <div class="footer-social">
                    <a href="https://uadeo.mx/" class="social-icon" title="UADEO" target="_blank">
                        <img src="assets/icon.svg" alt="UADEO" width="28" height="28" style="filter: brightness(0) saturate(100%) invert(85%) sepia(78%) saturate(2476%) hue-rotate(359deg) brightness(102%) contrast(93%);">
                    </a>
                    <a href="https://github.com/Hamialco/beta" class="social-icon" title="GitHub">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                    <a href="https://wa.me/526693313018" class="social-icon" title="WhatsApp">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.488"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Nivel Inferior: Enlaces -->
            <div class="footer-bottom">
                <div class="footer-links">
                    <ul>
                        <li><a href="#">Quiénes somos</a></li>
                        <li><a href="#">Misión y visión</a></li>
                        <li><a href="#">Contacto</a></li>
                        <li><a href="#">Soporte técnico</a></li>