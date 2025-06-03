<?php
session_start();
require_once 'config/database.php';

// Verificar que sea emprendedor
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'emprendedor') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch();

// Obtener proyectos del usuario
$stmt = $pdo->prepare("
    SELECT p.*, c.nombre as categoria_nombre,
           (SELECT AVG(estrellas) FROM resenas WHERE proyecto_id = p.id) as promedio_calificacion,
           (SELECT COUNT(*) FROM resenas WHERE proyecto_id = p.id) as total_resenas
    FROM proyectos p 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.usuario_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$proyectos = $stmt->fetchAll();

// Obtener categorías para el formulario
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY tipo, nombre");
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Emprendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .project-card {
            transition: transform 0.2s;
        }
        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s;
        }
        .upload-area:hover {
            border-color: #007bff;
        }
        .sidebar {
            background-color: #f8f9fa;
            min-height: calc(100vh - 76px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">🖼️ Galería Emprendedores</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Hola, <?php echo htmlspecialchars($usuario['nombre'] ?: $usuario['matricula']); ?></span>
                <a class="nav-link" href="index.php">Ver Galería</a>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar p-4">
                <h5>Panel de Control</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#proyectos" data-bs-toggle="tab">
                            📂 Mis Proyectos (<?php echo count($proyectos); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nuevo-proyecto" data-bs-toggle="tab">
                            ➕ Nuevo Proyecto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#perfil" data-bs-toggle="tab">
                            👤 Mi Perfil
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 p-4">
                <div class="tab-content">
                    <!-- Mis Proyectos -->
                    <div class="tab-pane fade show active" id="proyectos">
                        <h3 class="mb-4">Mis Proyectos</h3>
                        
                        <?php if (empty($proyectos)): ?>
                            <div class="text-center py-5">
                                <h5 class="text-muted">No tienes proyectos aún</h5>
                                <p>¡Crea tu primer proyecto para comenzar!</p>
                                <button class="btn btn-primary" onclick="document.querySelector('[href=\"#nuevo-proyecto\"]').click()">
                                    Crear Proyecto
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($proyectos as $proyecto): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card project-card h-100">
                                            <img src="<?php echo htmlspecialchars($proyecto['imagen_principal']); ?>" 
                                                 class="card-img-top" style="height: 200px; object-fit: cover;" 
                                                 alt="<?php echo htmlspecialchars($proyecto['titulo']); ?>">
                                            
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($proyecto['titulo']); ?></h6>
                                                <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($proyecto['categoria_nombre']); ?></span>
                                                
                                                <?php if ($proyecto['total_resenas'] > 0): ?>
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            ⭐ <?php echo number_format($proyecto['promedio_calificacion'], 1); ?>/5 
                                                            (<?php echo $proyecto['total_resenas']; ?> reseñas)
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <p class="card-text small"><?php echo substr(htmlspecialchars($proyecto['descripcion']), 0, 100); ?>...</p>
                                                
                                                <div class="badge bg-<?php echo $proyecto['estado'] === 'activo' ? 'success' : 'warning'; ?> mb-2">
                                                    <?php echo ucfirst($proyecto['estado']); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="card-footer">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editProject(<?php echo $proyecto['id']; ?>)">
                                                    Editar
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteProject(<?php echo $proyecto['id']; ?>)">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nuevo Proyecto -->
                    <div class="tab-pane fade" id="nuevo-proyecto">
                        <h3 class="mb-4">Crear Nuevo Proyecto</h3>
                        
                        <form id="projectForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="titulo" class="form-label">Título del Proyecto *</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" 
                                               maxlength="50" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="categoria_id" class="form-label">Categoría *</label>
                                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                                            <option value="">Selecciona una categoría</option>
                                            <?php 
                                            $current_tipo = '';
                                            foreach ($categorias as $categoria): 
                                                if ($categoria['tipo'] !== $current_tipo):
                                                    if ($current_tipo !== '') echo '</optgroup>';
                                                    echo '<optgroup label="' . ucfirst($categoria['tipo']) . 's">';
                                                    $current_tipo = $categoria['tipo'];
                                                endif;
                                            ?>
                                                <option value="<?php echo $categoria['id']; ?>">
                                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="contacto_whatsapp" class="form-label">WhatsApp</label>
                                        <input type="tel" class="form-control" id="contacto_whatsapp" 
                                               name="contacto_whatsapp" placeholder="+521234567890">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="contacto_instagram" class="form-label">Instagram</label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" class="form-control" id="contacto_instagram" 
                                                   name="contacto_instagram" placeholder="mi.emprendimiento">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                                  rows="4" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="imagen_principal" class="form-label">Imagen Principal *</label>
                                        <div class="upload-area">
                                            <input type="file" class="form-control" id="imagen_principal" 
                                                   name="imagen_principal" accept="image/*" required>
                                            <p class="text-muted mt-2">Arrastra tu imagen aquí o haz clic para seleccionar</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="imagenes_adicionales" class="form-label">Imágenes Adicionales (opcional)</label>
                                        <input type="file" class="form-control" id="imagenes_adicionales" 
                                               name="imagenes_adicionales[]" accept="image/*" multiple>
                                        <div class="form-text">Puedes seleccionar múltiples imágenes</div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Crear Proyecto</button>
                        </form>
                    </div>

                    <!-- Mi Perfil -->
                    <div class="tab-pane fade" id="perfil">
                        <h3 class="mb-4">Mi Perfil</h3>
                        
                        <form id="profileForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <div class="mb-3">
                                        <?php if ($usuario['foto_perfil']): ?>
                                            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" 
                                                 class="rounded-circle mb-3" width="150" height="150" 
                                                 style="object-fit: cover;" alt="Foto de perfil">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 150px; height: 150px;">
                                                <span class="text-white fs-1">👤</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <input type="file" class="form-control" id="foto_perfil" 
                                               name="foto_perfil" accept="image/*">
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               value="<?php echo htmlspecialchars($usuario['nombre'] ?: ''); ?>" 
                                               maxlength="50">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="carrera" class="form-label">Carrera</label>
                                                <input type="text" class="form-control" id="carrera" name="carrera" 
                                                       value="<?php echo htmlspecialchars($usuario['carrera'] ?: ''); ?>" 
                                                       maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="semestre" class="form-label">Semestre</label>
                                                <select class="form-select" id="semestre" name="semestre">
                                                    <option value="">Seleccionar</option>
                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                        <option value="<?php echo $i; ?>" 
                                                                <?php echo $usuario['semestre'] == $i ? 'selected' : ''; ?>>
                                                            <?php echo $i; ?>
                                                        </option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                                               value="<?php echo htmlspecialchars($usuario['telefono'] ?: ''); ?>" 
                                               maxlength="15">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="instagram_perfil" class="form-label">Instagram</label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" class="form-control" id="instagram_perfil" name="instagram" 
                                                   value="<?php echo htmlspecialchars($usuario['instagram'] ?: ''); ?>" 
                                                   maxlength="30">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="descripcion_perfil" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion_perfil" name="descripcion" 
                                                  rows="3" maxlength="150"><?php echo htmlspecialchars($usuario['descripcion'] ?: ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="motivacion" class="form-label">Motivación</label>
                                        <textarea class="form-control" id="motivacion" name="motivacion" 
                                                  rows="3" maxlength="200"><?php echo htmlspecialchars($usuario['motivacion'] ?: ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Manejar envío del formulario de proyecto
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('create-project.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('¡Proyecto creado exitosamente!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear el proyecto');
            });
        });

        // Manejar envío del formulario de perfil
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update-profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('¡Perfil actualizado exitosamente!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el perfil');
            });
        });

        function editProject(id) {
            // Implementar edición de proyecto
            alert('Función de edición en desarrollo');
        }

        function deleteProject(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este proyecto?')) {
                fetch('delete-project.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: id})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Proyecto eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el proyecto');
                });
            }
        }
    </script>
</body>
</html>