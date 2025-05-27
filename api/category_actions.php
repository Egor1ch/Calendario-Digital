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

function createCategory($conn, $usuario_id) {
    try {
        $nombre = $_POST['nombre'];
        $color = $_POST['color'];
        
        if (empty($nombre) || empty($color)) {
            echo json_encode(['success' => false, 'message' => 'El nombre y el color son obligatorios']);
            return;
        }
        
        $sql = "INSERT INTO categorias (usuario_id, nombre, color) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario_id, $nombre, $color]);
        

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

function updateCategory($conn, $usuario_id) {
    try {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $color = $_POST['color'];
        
        if (empty($nombre) || empty($color)) {
            echo json_encode(['success' => false, 'message' => 'El nombre y el color son obligatorios']);
            return;
        }
        
        $check_sql = "SELECT id FROM categorias WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar esta categoría']);
            return;
        }
        
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

function deleteCategory($conn, $usuario_id) {
    try {
        $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

        $check_sql = "SELECT id FROM categorias WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id, $usuario_id]);
        
        if ($check_stmt->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta categoría']);
            return;
        }
        

        $update_events_sql = "UPDATE eventos SET categoria = 'event' WHERE categoria = ? AND usuario_id = ?";
        $update_stmt = $conn->prepare($update_events_sql);
        $update_stmt->execute(["custom_" . $id, $usuario_id]);
        

        $sql = "DELETE FROM categorias WHERE id = ? AND usuario_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $usuario_id]);
        
        echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar categoría: ' . $e->getMessage()]);
    }
}

function getAllCategories($conn, $usuario_id) {
    try {
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
