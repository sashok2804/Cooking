<?php
include('../db.php'); // Подключение к базе данных

// Функция для получения деталей заказа и элементов заказа
function getOrderDetails($conn, $order_id) {
    // Получение основной информации о заказе
    $orderQuery = $conn->prepare("SELECT orders.id, orders.created_at, order_status.status_name
                                  FROM orders
                                  INNER JOIN order_status ON orders.status_id = order_status.id
                                  WHERE orders.id = ?");
    $orderQuery->bind_param("i", $order_id);
    $orderQuery->execute();
    $orderResult = $orderQuery->get_result()->fetch_assoc();
    $orderQuery->close();

    // Проверка существования заказа
    if (!$orderResult) {
        return null;
    }

    // Получение элементов заказа
    $itemsQuery = $conn->prepare("SELECT order_items.product_id, products.title, order_items.quantity, products.price
                                  FROM order_items
                                  INNER JOIN products ON order_items.product_id = products.id
                                  WHERE order_items.order_id = ?");
    $itemsQuery->bind_param("i", $order_id);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    $itemsQuery->close();

    // Добавление элементов заказа к информации о заказе
    $orderResult['order_items'] = $itemsResult;

    return $orderResult;
}

// Обработка POST запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Получен POST запрос');

    // Получение 'id' из POST данных
    $data = json_decode(file_get_contents('php://input'));
    $order_id = $data->id;
    if (!$order_id) {
        error_log('ID заказа не предоставлен');
        echo json_encode(['success' => false, 'message' => 'ID заказа не предоставлен']);
        exit;
    }

    // Логирование POST данных
    error_log('POST данные: ' . print_r($_POST, true));

    // Получение деталей заказа
    $orderDetails = getOrderDetails($conn, $order_id);
    if ($orderDetails) {
        echo json_encode(['success' => true, 'orderDetails' => $orderDetails]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Заказ не найден']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}
?>
