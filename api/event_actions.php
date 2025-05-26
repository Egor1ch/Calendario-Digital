<?php
session_start();
require_once "../db/config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión']);
    exit;
}

$usuario_id = $_SESSION["id"];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

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


function createEvent($conn, $usuario_id) {
    try {
        $titulo = $_POST['title'];
        $fecha = $_POST['date'];
        $hora = isset($_POST['time']) && !empty($_POST['time']) ? $_POST['time'] : null;
        $categoria = $_POST['category'];
        $descripcion = isset($_POST['description']) ? $_POST['description'] : '';
        
        $sql = "INSERT INTO eventos (usuario_id, titulo, fecha, hora, categoria, descripcion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario_id, $titulo, $fecha, $hora, $categoria, $descripcion]);
        
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

function updateEvent($conn, $usuario_id) {
    try {
        $id = $_POST['id'];
        $titulo = $_POST['title'];
        $fecha = $_POST['date'];
        $hora = isset($_POST['time']) && !empty($_POST['time']) ? $_POST['time'] : null;
        $categoria = $_POST['category'];
        $descripcion = isset($_POST['description']) ? $_POST['description'] : '';
        
        $check_sql = "SELECT id FROM eventos WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar este evento']);
            return;
        }
        
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


function deleteEvent($conn, $usuario_id) {
    try {
        $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
        
        $check_sql = "SELECT id FROM eventos WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar este evento']);
            return;
        }
        
        $sql = "DELETE FROM eventos WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $usuario_id]);
        
        echo json_encode(['success' => true, 'message' => 'Evento eliminado correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar evento: ' . $e->getMessage()]);
    }
}

function getAllEvents($conn, $usuario_id) {
    try {
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
