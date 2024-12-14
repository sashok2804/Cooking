<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id, role_id и данных из POST-запроса

// Проверка role_id перед выводом информации о пользователях
if ($role_id != 1) {
    http_response_code(403); // Forbidden
    $response = ['success' => false, 'message' => 'Ошибка авторизации'];
    echo json_encode($response);
    exit();
}

// Запрос для получения всех пользователей
$userQuery = "SELECT users.id, users.name, users.email, roles.title AS role_name
              FROM users
              INNER JOIN roles ON users.role_id = roles.id
              WHERE users.role_id = 4";

$userStmt = $conn->prepare($userQuery);
$userStmt->execute();
$userResult = $userStmt->get_result();
$users = $userResult->fetch_all(MYSQLI_ASSOC);

// Формирование ответа в формате JSON
$response = [
    'success' => true,
    'users' => $users
];

// Отправка ответа в формате JSON
header('Content-Type: application/json');
echo json_encode($response);

// Закрытие подготовленного запроса
$userStmt->close();

// Закрытие соединения с базой данных
$conn->close();
