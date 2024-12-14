<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена

// Функция для получения информации о заказах
function getOrders($conn, $user_id, $role_id) {
    $allowedStatuses = [
        1 => [1, 2, 3, 4, 5, 6], // Администратор (все статусы)
        2 => [1, 2, 3], // Повар (статусы 1, 2, 3)
        3 => [3, 4, 5], // Курьер (статусы 3, 4, 5)
        4 => [1, 2, 3, 4, 5, 6] // Клиент (все статусы)
    ];

    $allowedStatusesStr = implode(',', $allowedStatuses[$role_id]);

    if ($role_id == 4) {
        // Для клиента выполняем запрос с условием user_id
        $stmt = $conn->prepare("SELECT id, user_id, status_id, created_at FROM orders WHERE user_id = ? AND status_id IN ($allowedStatusesStr)");
        $stmt->bind_param("i", $user_id);
    } else {
        // Для других ролей выполняем запрос без условия user_id
        $stmt = $conn->prepare("SELECT id, user_id, status_id, created_at FROM orders WHERE status_id IN ($allowedStatusesStr)");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    } else {
        return null;
    }
}

// Получение заказов
$orders = getOrders($conn, $user_id, $role_id);

if ($orders === null) {
    http_response_code(404); // Not Found
    echo json_encode(['success' => false, 'message' => 'Заказы не найдены']);
} else {
    http_response_code(200); // OK
    echo json_encode(['success' => true, 'orders' => $orders]);
}

// Закрытие соединения с базой данных
$conn->close();
