<!DOCTYPE html>
<header class="header">
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