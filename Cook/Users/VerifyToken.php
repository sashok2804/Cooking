<?php
include('../db.php');

// Функция для проверки токена
function verifyToken($token) {
    global $conn; // Используйте ваш объект подключения к базе данных из db.php

    // Проверка наличия токена в базе данных
    $stmt = $conn->prepare("SELECT * FROM users WHERE authentication_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Токен найден, возвращаем роль пользователя
        return ['valid' => true, 'role_id' => $user['role_id']];
    }
    return ['valid' => false, 'role_id' => null];
}

// Получение токена из заголовка Authorization
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    error_log("Заголовок Authorization: " . $headers['Authorization']);
    preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
    if (isset($matches[1])) {
        $token = $matches[1];
        // Вызов функции проверки токена
        $tokenData = verifyToken($token);
        // Отправка результата клиенту
        echo json_encode($tokenData);
    } else {
        echo json_encode(['valid' => false, 'role_id' => null]);
    }
} else {
    echo json_encode(['valid' => false, 'role_id' => null]);
    error_log("Заголовок Authorization отсутствует");
}
