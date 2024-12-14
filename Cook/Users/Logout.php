<?php
include('../db.php');

// Получаем токен из заголовка авторизации
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];

    // Подготавливаем запрос на обновление токена в базе данных
    $stmt = $conn->prepare("UPDATE users SET authentication_token = NULL WHERE authentication_token = ?");
    $stmt->bind_param("s", $token);

    // Выполняем запрос и устанавливаем соответствующий статус код
    if ($stmt->execute()) {
        error_log("Токен успешно инвалидирован");
        http_response_code(200); // Успешно
    } else {
        error_log("Ошибка при инвалидации токена: " . $stmt->error);
        http_response_code(500); // Внутренняя ошибка сервера
    }

    $stmt->close();
} else {
    http_response_code(401); // Неавторизован
}

$conn->close();
?>
