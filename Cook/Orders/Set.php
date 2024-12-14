<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Подключение файла аутентификации

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    $response["success"] = false;
    $response["message"] = "Недопустимый метод запроса";
    echo json_encode($response);
    error_log("Ошибка: " . $response["message"]);
    exit;
}

// Получение данных из тела запроса
$data = json_decode(file_get_contents("php://input"), true);

// Проверка наличия необходимых данных
if (!isset($data['order_id'])) {
    http_response_code(400); // Bad Request
    $response["success"] = false;
    $response["message"] = "Отсутствуют необходимые данные";
    echo json_encode($response);
    error_log("Ошибка: " . $response["message"]);
    exit;
}

// Проверка действия (обновление статуса или отмена заказа)
$action = isset($data['status_id']) ? 'update_status' : 'cancel_order';

if ($action === 'update_status') {
    // Проверка допустимых статусов для каждой роли
    $allowedStatuses = [
        1 => [1, 2, 3, 4], // Администратор может ставить любой статус
        2 => [2, 3], // Пекарь может ставить статусы 2 и 3
        3 => [4, 5], // Курьер может ставить статусы 3 и 4
        4 => [] // Клиент не может менять статус
    ];

    // Проверка, может ли пользователь установить указанный статус
    if (!in_array($data['status_id'], $allowedStatuses[$role_id])) {
        http_response_code(403); // Forbidden
        $response["success"] = false;
        $response["message"] = "Недостаточно прав для изменения статуса";
        echo json_encode($response);
        error_log("Ошибка: " . $response["message"] . ", role_id: $role_id, status_id: " . $data['status_id']);
        exit;
    }

    // Обновление статуса заказа в базе данных
    $stmt = $conn->prepare("UPDATE orders SET status_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $data['status_id'], $data['order_id']);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        http_response_code(200); // OK
        $response["success"] = true;
        $response["message"] = "Статус заказа успешно обновлен";
        error_log("Успех: " . $response["message"] . ", order_id: " . $data['order_id'] . ", status_id: " . $data['status_id']);
    } else {
        http_response_code(500); // Internal Server Error
        $response["success"] = false;
        $response["message"] = "Ошибка при обновлении статуса заказа";
        error_log("Ошибка: " . $response["message"] . ", order_id: " . $data['order_id'] . ", status_id: " . $data['status_id']);
    }
} else if ($action === 'cancel_order') {
    // Проверка прав на отмену заказа
    $allowedRoles = [1]; // Только администратор может отменять заказы

    if (!in_array($role_id, $allowedRoles)) {
        http_response_code(403); // Forbidden
        $response["success"] = false;
        $response["message"] = "Недостаточно прав для отмены заказа";
        echo json_encode($response);
        error_log("Ошибка: " . $response["message"] . ", role_id: $role_id");
        exit;
    }

    // Отмена заказа (установка статуса 6 - отменен)
    $stmt = $conn->prepare("UPDATE orders SET status_id = 6 WHERE id = ?");
    $stmt->bind_param("i", $data['order_id']);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        http_response_code(200); // OK
        $response["success"] = true;

        $response["message"] = "Заказ успешно отменен";
        error_log("Успех: " . $response["message"] . ", order_id: " . $data['order_id']);
    } else {
        http_response_code(500); // Internal Server Error
        $response["success"] = false;
        $response["message"] = "Ошибка при отмене заказа";
        error_log("Ошибка: " . $response["message"] . ", order_id: " . $data['order_id']);
    }
} else {
    http_response_code(400); // Bad Request
    $response["success"] = false;
    $response["message"] = "Неверные данные запроса";
    error_log("Ошибка: " . $response["message"]);
}

echo json_encode($response);
