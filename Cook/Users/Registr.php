<?php
require_once('../db.php');

// Получаем данные из тела запроса
$rawData = file_get_contents("php://input");
$data = json_decode($rawData);

// Инициализация ответа
$response = ['success' => false, 'token' => ''];

if ($data) {
    // Экранирование входных данных
    $name = $conn->real_escape_string($data->name);
    $email = $conn->real_escape_string($data->email);
    $password = $conn->real_escape_string($data->password);
    $role_id = 4; // ID роли по умолчанию

    // Создание безопасного токена
    $token = bin2hex(openssl_random_pseudo_bytes(16));

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Подготовка запроса для вставки пользователя
    $query = "INSERT INTO users (name, email, password, role_id, authentication_token) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssis", $name, $email, $hashed_password, $role_id, $token);
    
    // Выполнение запроса и обработка результатов
    if ($stmt->execute()) {
        error_log("Новый пользователь добавлен: {$email}");
        $response['success'] = true;
        $response['token'] = $token;
        $response['role_id'] = $role_id;
        http_response_code(201); // Успешное создание ресурса
    } else {
        error_log("Ошибка добавления пользователя: {$stmt->error}");
        http_response_code(500); // Внутренняя ошибка сервера
    }

    // Закрытие подготовленного выражения
    $stmt->close();
} else {
    http_response_code(400); // Некорректный запрос
}

// Закрытие соединения с базой данных
$conn->close();

// Отправка JSON-ответа
header('Content-Type: application/json');
echo json_encode($response);
?>
