<?php
// Incluir configuración de sesión centralizada
require_once __DIR__ . '/session_config.php';

// Verificar estado del usuario
$usuario_logueado = is_logged_in();
$rol_usuario = get_user_role();

// Definir las opciones del menú según el rol
$menu_options = [];

if (!$usuario_logueado) {
    // Menú para visitantes
    $menu_options = [
        ['label' => 'Inicio', 'url' => 'index.php'],
        ['label' => 'Registrarse', 'url' => 'register.php'],
        ['label' => 'Iniciar sesión', 'url' => 'login.php'],
        ['label' => 'Ayuda', 'url' => 'ayuda.php']
    ];
} else {
    // Menús para usuarios logueados
    switch ($rol_usuario) {
        case 'estudiante':
            $menu_options = [
                ['label' => 'Inicio', 'url' => 'dashboard.php'],
                [
                    'label' => 'Productos', 
                    'url' => '#',
                    'submenu' => [
                        ['label' => 'Comida', 'url' => 'productos.php?categoria=1'],
                        ['label' => 'Ropa', 'url' => 'productos.php?categoria=2'],
                        ['label' => 'Arte', 'url' => 'productos.php?categoria=3'],
                        ['label' => 'Tecnología', 'url' => 'productos.php?categoria=4']
                    ]
                ],
                [
                    'label' => 'Servicios',
                    'url' => '#',
                    'submenu' => [
                        ['label' => 'Tutorías', 'url' => 'servicios.php?categoria=5'],
                        ['label' => 'Diseño Gráfico', 'url' => 'servicios.php?categoria=6'],
                        ['label' => 'Reparaciones', 'url' => 'servicios.php?categoria=7'],
                        ['label' => 'Eventos', 'url' => 'servicios.php?categoria=8']
                    ]
                ],
                ['label' => 'Emprender', 'url' => 'solicitud_emprendedor.php'],
                ['label' => 'Ayuda', 'url' => 'ayuda.php']
            ];
            break;
            
        case 'emprendedor':
            $menu_options = [
                ['label' => 'Inicio', 'url' => 'dashboard.php'],
                ['label' => 'Productos', 'url' => 'productos.php'],
                ['label' => 'Servicios', 'url' => 'servicios.php'],
                ['label' => 'Mi Perfil', 'url' => 'perfil.php'],
                ['label' => 'Emprender', 'url' => 'crear_proyecto.php'],
                ['label' => 'Ayuda', 'url' => 'ayuda.php']
            ];
            break;
            
        case 'admin':
            $menu_options = [
                ['label' => 'Inicio', 'url' => 'dashboard.php'],
                ['label' => 'Productos', 'url' => 'productos.php'],
                ['label' => 'Servicios', 'url' => 'servicios.php'],
                ['label' => 'Gestionar Sitio', 'url' => 'admin/gestionar.php'],
                ['label' => 'Solicitudes', 'url' => 'admin/solicitudes.php'],
                ['label' => 'Anunciar', 'url' => 'admin/publicidad.php'],
                ['label' => 'Reportes', 'url' => 'admin/reportes.php']
            ];
            break;
    }
}
?>

<div class="menu-container">
    <button class="menu-btn" id="menu-toggle">☰</button>
    <div class="menu-desplegable" id="menu-dropdown">
        <?php foreach ($menu_options as $option): ?>
            <?php if (isset($option['submenu'])): ?>
                <!-- Opción con submenú -->
                <div class="menu-item-with-submenu">
                    <a href="<?php echo $option['url']; ?>" class="menu-item-parent">
                        <?php echo $option['label']; ?>
                        <span class="submenu-arrow">▼</span>
                    </a>
                    <div class="submenu">
                        <?php foreach ($option['submenu'] as $subitem): ?>
                            <a href="<?php echo $subitem['url']; ?>" class="submenu-item">
                                <?php echo $subitem['label']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Opción normal -->
                <a href="<?php echo $option['url']; ?>"><?php echo $option['label']; ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
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
    min-width: 320px;
    border-radius: 0 0 8px 8px;
    z-index: 1000;
    border: 1px solid rgba(255,255,255,0.1);
    font-size: 1.5rem;
}

.menu-desplegable.active {
    display: block;
}

.menu-desplegable a {
    color: white;
    padding: 24px 32px;
    text-decoration: none;
    display: block;
    transition: background 0.3s;
}

.menu-desplegable a:hover {
    background: #A12B39;
}

/* Estilos para submenús */
.menu-item-with-submenu {
    position: relative;
}

.menu-item-parent {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
}

.submenu-arrow {
    font-size: 0.8em;
    transition: transform 0.3s;
}

.menu-item-with-submenu:hover .submenu-arrow {
    transform: rotate(180deg);
}

.submenu {
    background: #A12B39;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.menu-item-with-submenu:hover .submenu {
    max-height: 300px;
}

.submenu-item {
    padding: 16px 48px !important;
    font-size: 1.3rem !important;
    border-left: 3px solid #f1c40f;
}

.submenu-item:hover {
    background: #B83441 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuDropdown = document.getElementById('menu-dropdown');
    const menuContainer = document.querySelector('.menu-container');
    
    menuToggle.addEventListener('click', function() {
        menuDropdown.classList.toggle('active');
    });
    
    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (!menuContainer.contains(event.target)) {
            menuDropdown.classList.remove('active');
        }
    });
    
    // Prevenir que el menú se cierre al hacer clic en opciones con submenú
    const parentItems = document.querySelectorAll('.menu-item-parent');
    parentItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    });
});
</script>