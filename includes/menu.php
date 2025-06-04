<div class="menu-container">
  <button class="menu-btn" id="menu-toggle">☰</button>
  <div class="menu-desplegable" id="menu-dropdown">
    <a href="index.php">Inicio</a>
    <a href="registro.php">Registrarse</a>
    <a href="login.php">Iniciar sesión</a>
    <a href="ayuda.php">Ayuda</a>
  </div>
</div>

<style>
.menu-container {
  position: relative;
  display: inline-block;
}

.menu-btn {
  background: none;
  border: none;
  color: #f1c40f;
  font-size: 3rem;
  cursor: pointer;
  padding: 0.5rem;
  margin: 0;
}

.menu-desplegable {
  display: none;
  position: absolute;
  right: 0;
  background: #8B1A29;
  min-width: 320px; /* Antes 160px */
  border-radius: 0 0 8px 8px;
  z-index: 1000;
  border: 1px solid rgba(255,255,255,0.1);
  font-size: 1.5rem; /* Tamaño de letra más grande */
}

.menu-desplegable.active {
  display: block;
}

.menu-desplegable a {
  color: white;
  padding: 24px 32px; /* Antes 12px 16px */
  text-decoration: none;
  display: block;
  transition: background 0.3s;
}

.menu-desplegable a:hover {
  background: #A12B39;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menu-toggle');
  const menuDropdown = document.getElementById('menu-dropdown');
  
  menuToggle.addEventListener('click', function() {
    menuDropdown.classList.toggle('active');
  });
  
  // Cerrar menú al hacer clic fuera
  document.addEventListener('click', function(event) {
    if (!menuContainer.contains(event.target)) {
      menuDropdown.classList.remove('active');
    }
  });
});
</script>