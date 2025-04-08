<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Verificar si existe una cookie de "recordarme"
    if (isset($_COOKIE["remember_token"])) {
        require_once "db/config.php";
        
        $token = $_COOKIE["remember_token"];
        $stmt = $conn->prepare("SELECT id, nombre, email FROM usuarios WHERE remember_token = ?");
        $stmt->execute([$token]);
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $usuario["id"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["email"] = $usuario["email"];
        } else {
            header("Location: login.php");
            exit;
        }
    } else {
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Digital</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- El botón toggle y overlay se añadirán dinámicamente desde JavaScript -->
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-calendar-alt"></i>
                    <h1>Calendario</h1>
                </div>
            </div>
            
            <button id="create-event-sidebar" class="create-event-btn">
                <i class="fas fa-plus"></i>
                <span>Crear evento</span>
            </button>
            
            <!-- Mover el selector de vistas arriba de las categorías para móviles -->
            <div class="view-selector">
                <button data-view="year">
                    <i class="fas fa-calendar"></i>
                    <span>Año</span>
                </button>
                <button class="active" data-view="month">
                    <i class="fas fa-calendar-days"></i>
                    <span>Mes</span>
                </button>
                <button data-view="week">
                    <i class="fas fa-calendar-week"></i>
                    <span>Semana</span>
                </button>
                <button data-view="day">
                    <i class="fas fa-calendar-day"></i>
                    <span>Día</span>
                </button>
            </div>
            
            <!-- Categorías ahora van después del selector de vistas -->
            <div class="categories-section">
                <div class="section-header">
                    <h3>Categorías</h3>
                    <div class="section-actions">
                        <button id="add-category-btn" class="add-category-btn" title="Añadir nueva categoría">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="toggle-section"><i class="fas fa-chevron-down"></i></button>
                    </div>
                </div>
                <div class="categories-list">
                    <div class="category-item default-category">
                        <input type="checkbox" id="cat-events" checked>
                        <label for="cat-events">
                            <span class="color-dot" style="background-color: var(--event-color);"></span>
                            <span>Eventos</span>
                        </label>
                    </div>
                    <div class="category-item default-category">
                        <input type="checkbox" id="cat-parties" checked>
                        <label for="cat-parties">
                            <span class="color-dot" style="background-color: var(--party-color);"></span>
                            <span>Fiestas</span>
                        </label>
                    </div>
                    <!-- Las categorías personalizadas se añadirán aquí dinámicamente -->
                </div>
            </div>
            
            <!-- Mini calendario ahora después de categorías, será ocultado en móviles por CSS -->
            <div class="mini-calendar-wrapper">
                <div class="mini-calendar" id="mini-calendar"></div>
                <div class="mini-calendar-nav">
                    <button id="mini-prev"><i class="fas fa-chevron-left"></i></button>
                    <button id="mini-next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <div class="toggles-container">
                    <div class="theme-toggle">
                        <i class="fas fa-sun"></i>
                        <label class="switch">
                            <input type="checkbox" id="theme-toggle">
                            <span class="slider round"></span>
                        </label>
                        <i class="fas fa-moon"></i>
                    </div>
                </div>
            </div>
        </aside>
        <main class="calendar-container">
            <div class="calendar-header">
                <div class="header-top-row">
                    <div class="current-date">
                        <h2 id="current-month-year"></h2>
                    </div>
                    
                    <!-- Información del usuario en la parte superior derecha -->
                    <div class="user-info-top">
                        <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>
                            <a href="auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                        </div>
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="calendar-nav">
                    <button id="prev-btn"><i class="fas fa-chevron-left"></i></button>
                    <button id="today-btn">Hoy</button>
                    <button id="next-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-views">
                <div id="year-view" class="calendar-view" style="display: none;">
                    <div class="year-grid" id="year-grid"></div>
                </div>
                <div id="month-view" class="calendar-view active">
                    <div class="weekdays">
                        <div>Lun</div>
                        <div>Mar</div>
                        <div>Mié</div>
                        <div>Jue</div>
                        <div>Vie</div>
                        <div>Sáb</div>
                        <div>Dom</div>
                    </div>
                    <div class="days" id="calendar-days"></div>
                </div>
                <div id="week-view" class="calendar-view" style="display: none; height: 100%; overflow: hidden;"></div>
                <div id="day-view" class="calendar-view" style="display: none; height: 100%; overflow: hidden;"></div>
            </div>
        </main>
    </div>

    <!-- Modal para añadir/editar eventos -->
    <div class="modal" id="event-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modal-title">Nuevo Evento</h2>
            <form id="event-form">
                <input type="hidden" id="event-id">
                <input type="hidden" id="user-id" value="<?php echo $_SESSION["id"]; ?>">
                <div class="form-group">
                    <label for="event-title">Título</label>
                    <input type="text" id="event-title" required>
                </div>
                <div class="form-group">
                    <label for="event-date">Fecha</label>
                    <input type="date" id="event-date" required>
                </div>
                <div class="form-group">
                    <label for="event-time">Hora</label>
                    <input type="time" id="event-time">
                </div>
                <div class="form-group">
                    <label for="event-category">Categoría</label>
                    <select id="event-category">
                        <option value="event">Evento</option>
                        <option value="party">Fiesta</option>
                        <!-- Las categorías personalizadas se añadirán aquí dinámicamente -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="event-description">Descripción</label>
                    <textarea id="event-description"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit">Guardar</button>
                    <button type="button" id="delete-btn" class="delete-btn">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para añadir/editar categorías -->
    <div class="modal" id="category-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Nueva Categoría</h2>
            <form id="category-form">
                <input type="hidden" id="category-id">
                <div class="form-group">
                    <label for="category-name">Nombre</label>
                    <input type="text" id="category-name" required>
                </div>
                <div class="form-group">
                    <label for="category-color">Color</label>
                    <input type="color" id="category-color" required>
                </div>
                <div class="form-actions">
                    <button type="submit">Guardar</button>
                    <button type="button" id="delete-category-btn" class="delete-btn">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/calendar.js"></script>
    <script src="js/categories.js"></script>
    <script src="js/events.js"></script>
</body>
</html>
