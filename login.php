<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_POST) {
    $matricula = $_POST['matricula'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($matricula && $password) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE matricula = ? AND password = ?");
        $stmt->execute([$matricula, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['matricula'] = $user['matricula'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombre'] = $user['nombre'];
            
            if ($user['rol'] === 'emprendedor') {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');
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
    <title>Iniciar Sesión - Galería Emprendedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/styles.css" rel="stylesheet">
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
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="matricula" class="form-label">Matrícula</label>
                                    <input type="text" class="form-control" id="matricula" name="matricula" 
                                           pattern="2[0-9]{7}" 
                                           maxlength="8"
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6" maxlength="8" 
                                           required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
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
</body>
</html>