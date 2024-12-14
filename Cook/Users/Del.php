<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка role_id перед получением информации о сменах
if ($role_id != 1) {
    http_response_code(403); // Forbidden
    $errorMessage = 'Ошибка авторизации';
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit();
}

// Получение данных из тела запроса
$data = json_decode(file_get_contents("php://input"));

// Проверка наличия id пользователя для удаления
if (!isset($data->id)) {
    http_response_code(400); // Bad Request
    $errorMessage = 'Не указан ID пользователя для удаления';
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit();
}

// Подготовка запроса на удаление пользователя
$userId = $data->id;
$newRoleId = 5;
$sql = "UPDATE users SET role_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $newRoleId, $userId);

// Выполнение запроса
if ($stmt->execute()) {
    http_response_code(200); // OK
    echo json_encode(['success' => true, 'message' => 'Пользователь успешно удален']);
} else {
    http_response_code(500); // Internal Server Error
    $errorMessage = 'Ошибка при удалении пользователя: ' . $stmt->error;
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}

// Закрытие подготовленного запроса
$stmt->close();
