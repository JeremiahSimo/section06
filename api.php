<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once 'db_config.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'get_items':
        getItems($conn);
        break;
    case 'add_item':
        addItem($conn);
        break;
    case 'update_item':
        updateItem($conn);
        break;
    case 'delete_item':
        deleteItem($conn);
        break;
    case 'get_dashboard_stats':
        getDashboardStats($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

function getDashboardStats($conn) {
    $stats = [];

    // Get total products, quantity, and value
    $result = $conn->query("SELECT COUNT(*) as total_products, SUM(quantity) as total_quantity, SUM(quantity * price) as total_value FROM products");
    $stats = $result->fetch_assoc();

    // Get recent items
    $result = $conn->query("SELECT name, created_at FROM products ORDER BY created_at DESC LIMIT 5");
    $recent_items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recent_items[] = $row;
        }
    }
    $stats['recent_items'] = $recent_items;

    // Get all items for chart
    $result = $conn->query("SELECT name, quantity FROM products ORDER BY quantity DESC");
    $chart_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $chart_data[] = $row;
        }
    }
    $stats['chart_data'] = $chart_data;

    echo json_encode($stats);
}



function getItems($conn) {
    $sql = "SELECT id, name, description, quantity, price, created_at FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    $items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    echo json_encode($items);
}

function addItem($conn) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;

    if (empty($name) || $quantity < 0 || $price < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $name, $description, $quantity, $price);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding item: ' . $stmt->error]);
    }
    $stmt->close();
}

function updateItem($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;

    if ($id <= 0 || empty($name) || $quantity < 0 || $price < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, quantity = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssidi", $name, $description, $quantity, $price, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating item: ' . $stmt->error]);
    }
    $stmt->close();
}

function deleteItem($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting item: ' . $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
