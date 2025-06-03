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
                $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
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
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" id="registerForm">
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
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6" maxlength="8" 
                                           required>
                                    <div id="passwordMatch" class="match-indicator" style="display: none;"></div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">Registrarse</button>
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
                    matchIndicator.textContent = '✓ Las contraseñas coinciden';
                    matchIndicator.className = 'match-indicator success';
                } else {
                    confirmPassword.classList.remove('password-match');
                    confirmPassword.classList.add('password-mismatch');
                    matchIndicator.textContent = '✗ Las contraseñas no coinciden';
                    matchIndicator.className = 'match-indicator error';
                }
            }
            
            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
        });
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>