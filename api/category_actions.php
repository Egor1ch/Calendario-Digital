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
        createCategory($conn, $usuario_id);
        break;
    case 'update':
        updateCategory($conn, $usuario_id);
        break;
    case 'delete':
        deleteCategory($conn, $usuario_id);
        break;
    case 'getAll':
        getAllCategories($conn, $usuario_id);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

// Función para crear una nueva categoría
function createCategory($conn, $usuario_id) {
    try {
        // Obtener datos de la categoría
        $nombre = $_POST['nombre'];
        $color = $_POST['color'];
        
        // Validar datos
        if (empty($nombre) || empty($color)) {
            echo json_encode(['success' => false, 'message' => 'El nombre y el color son obligatorios']);
            return;
        }
        
        // Preparar consulta SQL
        $sql = "INSERT INTO categorias (usuario_id, nombre, color) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario_id, $nombre, $color]);
        
        // Obtener el ID de la categoría recién creada
        $category_id = $conn->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Categoría creada correctamente',
            'category' => [
                'id' => $category_id,
                'nombre' => $nombre,
                'color' => $color
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear categoría: ' . $e->getMessage()]);
    }
}

// Función para actualizar una categoría existente
function updateCategory($conn, $usuario_id) {
    try {
        // Obtener datos de la categoría
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $color = $_POST['color'];
        
        // Validar datos
        if (empty($nombre) || empty($color)) {
            echo json_encode(['success' => false, 'message' => 'El nombre y el color son obligatorios']);
            return;
        }
        
        // Verificar si la categoría pertenece al usuario
        $check_sql = "SELECT id FROM categorias WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar esta categoría']);
            return;
        }
        
        // Preparar consulta SQL
        $sql = "UPDATE categorias SET nombre = ?, color = ? WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $color, $id, $usuario_id]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Categoría actualizada correctamente',
            'category' => [
                'id' => $id,
                'nombre' => $nombre,
                'color' => $color
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar categoría: ' . $e->getMessage()]);
    }
}

// Función para eliminar una categoría
function deleteCategory($conn, $usuario_id) {
    try {
        // Obtener ID de la categoría
        $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
        
        // Verificar si la categoría pertenece al usuario
        $check_sql = "SELECT id FROM categorias WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta categoría']);
            return;
        }
        
        // Primero actualizar eventos que usen esta categoría a una categoría por defecto
        $update_events_sql = "UPDATE eventos SET categoria = 'event' WHERE categoria = ? AND usuario_id = ?";
        $update_stmt = $conn->prepare($update_events_sql);
        $update_stmt->execute(["custom_" . $id, $usuario_id]);
        
        // Ahora eliminar la categoría
        $sql = "DELETE FROM categorias WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $usuario_id]);
        
        echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar categoría: ' . $e->getMessage()]);
    }
}

// Función para obtener todas las categorías del usuario
function getAllCategories($conn, $usuario_id) {
    try {
        // Preparar consulta SQL
        $sql = "SELECT id, nombre, color FROM categorias WHERE usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario_id]);
        
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'categories' => $categories]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener categorías: ' . $e->getMessage()]);
    }
}
?>
