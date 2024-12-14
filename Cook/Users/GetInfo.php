<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена

$response = array();

// Проверка наличия токена
if ($token) {
    // Подготовка запроса к базе данных для получения информации о пользователе
    $stmt = $conn->prepare("SELECT id, name, email, role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Проверка наличия пользователя и вывод информации
    if ($user) {
        // Получение названия роли пользователя
        $roleStmt = $conn->prepare("SELECT title FROM roles WHERE id = ?");
        $roleStmt->bind_param("i", $user['role_id']);
        $roleStmt->execute();
        $roleResult = $roleStmt->get_result();
        $role = $roleResult->fetch_assoc();

        $user['role_name'] = $role['title'];
        echo json_encode($user);
    } else {
        http_response_code(404); // Not Found
        $response["success"] = false;
        $response["message"] = "Пользователь не найден";
        echo json_encode($response);
        exit;
    }
    // Закрытие подготовленных запросов
    $stmt->close();
    $roleStmt->close();
}
