<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id, role_id и данных из POST-запроса

// Проверка role_id перед выводом информации о пользователе
if ($role_id != 1) {
    http_response_code(403); // Forbidden
    $response = ['success' => false, 'message' => 'Ошибка авторизации'];
    echo json_encode($response);
    exit();
}

// Получение user_id из запроса
$userId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$userId) {
    http_response_code(400); // Bad Request
    $response = ['success' => false, 'message' => 'Отсутствует id пользователя'];
    echo json_encode($response);
    exit();
}

// Запрос для получения информации о пользователе
$userQuery = "SELECT u.id, u.name, u.email, u.authentication_token, r.title AS role_name
              FROM users u
              INNER JOIN roles r ON u.role_id = r.id
              WHERE u.id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
    http_response_code(404); // Not Found
    $response = ['success' => false, 'message' => 'Пользователь не найден'];
    echo json_encode($response);
    exit();
}

// Запрос для получения корзины пользователя
$cartQuery = "SELECT c.id, p.title, p.description, p.price, p.image_path, c.quantity
              FROM cart c
              INNER JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?";
$cartStmt = $conn->prepare($cartQuery);
$cartStmt->bind_param("i", $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();
$cart = $cartResult->fetch_all(MYSQLI_ASSOC);

// Запрос для получения заказов пользователя
$ordersQuery = "SELECT o.id, o.created_at, os.status_name, oi.quantity, p.title, p.price
                FROM orders o
                INNER JOIN order_status os ON o.status_id = os.id
                INNER JOIN order_items oi ON o.id = oi.order_id
                INNER JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = ?";
$ordersStmt = $conn->prepare($ordersQuery);
$ordersStmt->bind_param("i", $userId);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();
$orders = $ordersResult->fetch_all(MYSQLI_ASSOC);

// Формирование ответа в формате JSON
$response = [
    'success' => true,
    'user' => $user,
    'cart' => $cart,
    'orders' => $orders
];

// Отправка ответа в формате JSON
header('Content-Type: application/json');
echo json_encode($response);

// Закрытие подготовленных запросов
$userStmt->close();
$cartStmt->close();
$ordersStmt->close();

// Закрытие соединения с базой данных
$conn->close();
