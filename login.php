<?php
// Incluir configuración de sesión centralizada
require_once 'includes/session_config.php';
require_once 'config/database.php';

// Si ya está logueado, redirigir
if (is_logged_in()) {
    $role = get_user_role();
    if ($role === 'admin') {
        header('Location: admin.php');
    } else {
        // Tanto estudiantes como emprendedores van al dashboard
        header('Location: dashboard.php');
    }
    exit;
}

$error = '';

if ($_POST) {
    $matricula = $_POST['matricula'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($matricula && $password) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE matricula = ? AND password = ?");
        $stmt->execute([$matricula, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Configurar sesión para que se cierre al cerrar navegador
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['matricula'] = $user['matricula'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombre'] = $user['nombre'];
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            // Nueva lógica de redirección
            if ($user['rol'] === 'admin') {
                header('Location: admin.php');
            } else {
                // Tanto estudiantes como emprendedores van al dashboard
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $error = 'Matrícula o contraseña incorrectos.';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MML</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/styles.css" rel="stylesheet">
    <style>
        /* LOGIN - Estilos específicos */
        .login-body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .login-form {
            width: 100%;
            max-width: 600px;
        }

        .login-form h2 {
            color: #f1c40f;
            margin-bottom: 3rem;
            font-size: 2.25rem;
            font-weight: 700;
        }

        .form-container {
            padding: 3rem 1.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.75rem;
            color: #333;
            font-size: 1.2rem;
        }

        .login-form .form-label {
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .form-control {
            border: 3px solid #e9ecef;
            border-radius: 12px;
            padding: 1.125rem;
            font-size: 1.1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #f1c40f;
            box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
            background: white;
        }

        .btn-primary {
            background: #f1c40f;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            color: #2c3e50;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 12px;
            padding: 1.125rem;
            font-size: 1.1rem;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            border-radius: 12px;
            padding: 1.125rem;
            font-size: 1.1rem;
        }

        /* Enlaces */
        .text-center a { 
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #f1c40f;
            text-decoration: underline;
        }

        .text-muted {
            color: white !important;
        }

        .text-muted:hover {
            color: #f1c40f !important;
        }

        .welcome-text {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
            font-size: 1.5rem;
            line-height: 2;
            font-weight: 550;
        }

        /* Responsive para login */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 1rem;
            }
            
            .login-form {
                margin: 0.5rem;
            }
        }

        @media (max-width: 400px) {    
            .form-control {
                padding: 0.6rem;
            }
            
            .btn-primary {
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body class="login-body">
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
                    <div class="login-form">
                        <div class="form-container">
                            <h2 class="text-center">Iniciar Sesión</h2>
                            <p class="welcome-text">
                                Ya puedes enterarte de los mejores productos y servicios de la comunidad lince ¡Incluso tener tu propio emprendimiento!
                            </p>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" id="loginForm">
                                <div class="mb-3">
                                    <label for="matricula" class="form-label">Matrícula</label>
                                    <input type="text" class="form-control" id="matricula" name="matricula" 
                                           pattern="2[0-9]{7}" 
                                           maxlength="8"
                                           value="<?php echo htmlspecialchars($_POST['matricula'] ?? ''); ?>" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6" maxlength="8" 
                                           required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">Entrar</button>
                            </form>
                            
                            <div class="text-center">
                                <p class="mb-2">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
                                <a href="index.php" class="text-muted">Volver al inicio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Script para detectar cierre de pestaña/navegador -->
    <script>
    // Detectar cuando se cierra la pestaña o navegador
   // window.addEventListener('beforeunload', function(e) {
        // Enviar solicitud para limpiar sesión del lado del servidor
       // navigator.sendBeacon('includes/cleanup_session.php');
    //});
    // También detectar cuando la página pierde el foco por mucho tiempo
    let pageHidden = false;
    let hiddenTime = null;
    
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            pageHidden = true;
            hiddenTime = Date.now();
        } else {
            if (pageHidden && hiddenTime) {
                // Si la página estuvo oculta por más de 30 minutos, cerrar sesión
                const timeDiff = Date.now() - hiddenTime;
                if (timeDiff > 1800000) { // 30 minutos en milisegundos
                    window.location.href = 'includes/cleanup_session.php?redirect=true';
                }
            }
            pageHidden = false;
            hiddenTime = null;
        }
    });
    </script>
</body>
</html>