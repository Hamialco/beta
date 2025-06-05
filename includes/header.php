<!DOCTYPE html>
<header class="header">
  <style>
/* Estilos Generales para el Header */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
}

/* Header */
.header {
    background: #8B1A29;
    padding: 1.5rem;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1800px;
    margin: 0 auto;
    width: 100%;
    gap: 1.5rem;
}

.header-logo {
    height: 75px;
    transition: height 0.3s ease;
}

.btn-header {
    text-decoration: none;
    background: #1f4f82;
    color: #ffffff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1.05rem;
    transition: background-color 0.3s ease;
}

.btn-header:hover {
    background: #2c5aa0;
    color: #ffffff;
}

.btn-register {
    text-decoration: none;
    background: #1f4f82;
    color: white;
    border: none;
    padding: 0.75rem 1.8rem;
    border-radius: 15px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1.35rem;
    transition: all 0.3s ease;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.btn-center {
    text-decoration: none;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

/* Responsive Design para Header */
@media (max-width: 768px) {
    .header-logo {
        height: 60px;
    }
    
    .btn-register {
        font-size: 1.2rem;
        padding: 0.6rem 1.5rem;
    }
}

@media (max-width: 576px) {
    .header {
        padding: 1rem;
    }
    
    .header-logo {
        height: 50px;
    }
    
    .btn-register {
        font-size: 1rem;
        padding: 0.5rem 1.2rem;
    }
    
    .header-content {
        gap: 1rem;
    }
}
</style>
  <div class="header-content">
    <a href="index.php">
      <img src="assets/logo.svg" alt="Logo" class="header-logo">
    </a>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="register.php" class="btn-register">Unirse</a>
    <?php endif; ?>
    
    <?php include 'includes/menu.php'; ?>
  </div>
</header>