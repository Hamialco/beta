<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_POST) {
    $matricula = trim($_POST['matricula'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validaciones
    if (!preg_match('/^2[0-9]{7}$/', $matricula)) {
        $error = 'La matrícula debe comenzar con 2 y tener 8 dígitos en total';
    } elseif (strlen($password) < 6 || strlen($password) > 8) {
        $error = 'La contraseña debe tener entre 6 y 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verificar si la matrícula ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE matricula = ?");
        $stmt->execute([$matricula]);
        
        if ($stmt->fetch()) {
            $error = 'Esta matrícula ya está registrada';
        } else {
            // Insertar nuevo usuario
            $stmt = $pdo->prepare("INSERT INTO usuarios (matricula, password, rol) VALUES (?, ?, 'estudiante')");
            
            if ($stmt->execute([$matricula, $password])) {
                $success = 'Registro exitoso. Serás redirigido al login en 3 segundos...';
            } else {
                $error = 'Error al registrar el usuario';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Galería Emprendedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/styles.css" rel="stylesheet">
    <style>
        /* REGISTER - Estilos específicos */
        .register-body {
            background: white;
        }

        .register-form {
            width: 100%;
            max-width: 600px;
        }

        .register-form h2 {
            color: #1f4f82;
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

        .register-form .form-label {
            color: #2c3e50;
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
            border-color: #1f4f82;
            box-shadow: 0 0 0 0.2rem rgba(31, 79, 130, 0.25);
            background: white;
        }

        .btn-primary {
            background: #1f4f82;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: #2c5aa0;
            transform: translateY(-1px);
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

        /* Validación en tiempo real */
        .password-match {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }

        .password-mismatch {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .match-indicator {
            font-size: 1.3125rem;
            margin-top: 0.375rem;
            font-weight: 500;
        }

        .match-indicator.success {
            color: #28a745;
        }

        .match-indicator.error {
            color: #dc3545;
        }

        /* Enlaces */
        .text-center a {
            color: #1f4f82;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #2c5aa0;
            text-decoration: underline;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-muted:hover {
            color: #495057 !important;
        }

        .welcome-text {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 3rem;
            font-size: 1.5rem;
            line-height: 2;
            font-weight: 550;
        }

        /* Responsive para register */
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
            
            .register-form {
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
<body class="register-body">
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
                    <div class="register-form">
                        <div class="form-container">
                            <h2 class="text-center">Regístrate</h2>
                            <p class="welcome-text">
                                Obten todos los detalles de las ofertas que la comunidad emprendedora tiene para ti y contacta con los emprendedores facilmente.
                            </p>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" id="registerForm">
                                <div class="mb-3">
                                    <label for="matricula" class="form-label">Matrícula:</label>
                                    <input type="text" class="form-control" id="matricula" name="matricula" 
                                           pattern="2[0-9]{7}" 
                                           maxlength="8"
                                           value="<?php echo htmlspecialchars($_POST['matricula'] ?? ''); ?>" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña:</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6" maxlength="8" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirmar Contraseña:</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6" maxlength="8" 
                                           required>
                                    <div id="passwordMatch" class="match-indicator" style="display: none;"></div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">Unirse</button>
                            </form>
                            
                            <div class="text-center">
                                <p class="mb-2">¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                                <a href="index.php" class="text-muted">Volver al inicio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const matchIndicator = document.getElementById('passwordMatch');
            const submitBtn = document.getElementById('submitBtn');
            
            function checkPasswordMatch() {
                const pass1 = password.value;
                const pass2 = confirmPassword.value;
                
                if (pass2.length === 0) {
                    matchIndicator.style.display = 'none';
                    confirmPassword.classList.remove('password-match', 'password-mismatch');
                    return;
                }
                
                matchIndicator.style.display = 'block';
                
                if (pass1 === pass2) {
                    confirmPassword.classList.remove('password-mismatch');
                    confirmPassword.classList.add('password-match');
                    matchIndicator.textContent = 'Las contraseñas coinciden';
                    matchIndicator.className = 'match-indicator success';
                } else {
                    confirmPassword.classList.remove('password-match');
                    confirmPassword.classList.add('password-mismatch');
                    matchIndicator.textContent = 'Las contraseñas no coinciden';
                    matchIndicator.className = 'match-indicator error';
                }
            }
            
            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
            
            // Redirección automática después del registro exitoso
            <?php if ($success): ?>
                setTimeout(function() {
                    window.location.href = 'login.php?registered=1';
                }, 3000);
            <?php endif; ?>
        });
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>