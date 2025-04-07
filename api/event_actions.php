<?php
session_start();
require_once "../db/config.php";

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión']);
    exit;
}

$usuario_id = $_SESSION["id"];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Manejar diferentes acciones
switch ($action) {
    case 'create':
        createEvent($conn, $usuario_id);
        break;
    case 'update':
        updateEvent($conn, $usuario_id);
        break;
    case 'delete':
        deleteEvent($conn, $usuario_id);
        break;
    case 'getAll':
        getAllEvents($conn, $usuario_id);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

// Función para crear un nuevo evento
function createEvent($conn, $usuario_id) {
    try {
        // Obtener datos del evento
        $titulo = $_POST['title'];
        $fecha = $_POST['date'];
        $hora = isset($_POST['time']) && !empty($_POST['time']) ? $_POST['time'] : null;
        $categoria = $_POST['category'];
        $descripcion = isset($_POST['description']) ? $_POST['description'] : '';
        
        // Preparar consulta SQL
        $sql = "INSERT INTO eventos (usuario_id, titulo, fecha, hora, categoria, descripcion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario_id, $titulo, $fecha, $hora, $categoria, $descripcion]);
        
        // Obtener el ID del evento recién creado
        $event_id = $conn->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Evento creado correctamente',
            'event' => [
                'id' => $event_id,
                'title' => $titulo,
                'date' => $fecha,
                'time' => $hora,
                'category' => $categoria,
                'description' => $descripcion
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear evento: ' . $e->getMessage()]);
    }
}

// Función para actualizar un evento existente
function updateEvent($conn, $usuario_id) {
    try {
        // Obtener datos del evento
        $id = $_POST['id'];
        $titulo = $_POST['title'];
        $fecha = $_POST['date'];
        $hora = isset($_POST['time']) && !empty($_POST['time']) ? $_POST['time'] : null;
        $categoria = $_POST['category'];
        $descripcion = isset($_POST['description']) ? $_POST['description'] : '';
        
        // Verificar si el evento pertenece al usuario
        $check_sql = "SELECT id FROM eventos WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar este evento']);
            return;
        }
        
        // Preparar consulta SQL
        $sql = "UPDATE eventos SET titulo = ?, fecha = ?, hora = ?, categoria = ?, descripcion = ? 
                WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$titulo, $fecha, $hora, $categoria, $descripcion, $id, $usuario_id]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Evento actualizado correctamente',
            'event' => [
                'id' => $id,
                'title' => $titulo,
                'date' => $fecha,
                'time' => $hora,
                'category' => $categoria,
                'description' => $descripcion
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar evento: ' . $e->getMessage()]);
    }
}

// Función para eliminar un evento
function deleteEvent($conn, $usuario_id) {
    try {
        // Obtener ID del evento
        $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
        
        // Verificar si el evento pertenece al usuario
        $check_sql = "SELECT id FROM eventos WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar este evento']);
            return;
        }
        
        // Preparar consulta SQL
        $sql = "DELETE FROM eventos WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $usuario_id]);
        
        echo json_encode(['success' => true, 'message' => 'Evento eliminado correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar evento: ' . $e->getMessage()]);
    }
}

// Función para obtener todos los eventos del usuario
function getAllEvents($conn, $usuario_id) {
    try {
        // Preparar consulta SQL
        $sql = "SELECT id, titulo, fecha, hora, categoria, descripcion FROM eventos WHERE usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario_id]);
        
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'events' => $events]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener eventos: ' . $e->getMessage()]);
    }
}
?>
