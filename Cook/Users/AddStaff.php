<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id, role_id и данных из POST-запроса

// Декодирование JSON в ассоциативный массив
$data = json_decode(file_get_contents("php://input"), true);

// Проверка role_id перед добавлением пользователя
if ($role_id != 1) {
    http_response_code(403); // Forbidden
    $errorMessage = 'Ошибка авторизации';
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit();
}

// Проверка наличия данных
if (!$data || !isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
    http_response_code(400); // Bad Request
    $errorMessage = 'Недостаточно данных для добавления пользователя';
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit();
}

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT); // Хеширование пароля
$role_id = $data['role'];

// Подготовка SQL-запроса
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $name, $email, $password, $role_id);

// Выполнение запроса
if ($stmt->execute()) {
    http_response_code(200); // OK
    echo json_encode(['success' => true, 'message' => 'Пользователь успешно добавлен']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении пользователя']);
}

// Закрытие подготовленного запроса
$stmt->close();

// Закрытие соединения с базой данных
$conn->close();