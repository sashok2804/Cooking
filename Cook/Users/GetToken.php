<?php
include('../db.php');

// Функция для получения user_id и role_id по токену
function getUserDataFromToken($conn, $token) {
    $stmt = $conn->prepare("SELECT id, role_id FROM users WHERE authentication_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return [$row['id'], $row['role_id']];
    } else {
        return [null, null];
    }
}

$headers = getallheaders();

// Проверка наличия заголовка Authorization
if (!isset($headers['Authorization'])) {
    http_response_code(401); // Unauthorized
    $response["success"] = false;
    $response["message"] = "Токен не предоставлен";
    echo json_encode($response);
    exit;
}

// Извлечение токена из заголовка
list($token) = sscanf($headers['Authorization'], 'Bearer %s');

if (!$token) {
    http_response_code(400); // Bad Request
    $response["success"] = false;
    $response["message"] = "Токен не действителен";
    echo json_encode($response);
    exit;
}

// Получение user_id и role_id из токена
list($user_id, $role_id) = getUserDataFromToken($conn, $token);

if ($user_id === null || $role_id === null) {
    http_response_code(404); // Not Found
    $response["success"] = false;
    $response["message"] = "Пользователь не найден";
    echo json_encode($response);
    exit;
}

if (!$user_id) {
    http_response_code(401); // Unauthorized
    $response["success"] = false;
    $response["message"] = "Аутентификация не удалась";
    echo json_encode($response);
    exit;
}
