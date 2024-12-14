<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка role_id перед выполнением операций
if ($role_id == 4) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Ошибка авторизации']);
    exit();
}

// Функция для проверки, существует ли уже запись пользователя на смену
function checkIfSignedUp($conn, $user_id, $shift_id) {
    $stmt = $conn->prepare("SELECT * FROM Shift_Participation WHERE user_id = ? AND shift_id = ?");
    $stmt->bind_param("ii", $user_id, $shift_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'Вы уже записаны на эту смену']);
        exit();
    }
}

// Функция для записи пользователя на смену
function signUpForShift($conn, $user_id, $shift_id) {
    checkIfSignedUp($conn, $user_id, $shift_id); // Проверка на уникальность записи
    $stmt = $conn->prepare("INSERT INTO Shift_Participation (user_id, shift_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $shift_id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        return true;
    } else {
        return false;
    }
}

// Обработка POST запроса для записи на смену
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data->shift_id)) {
    $shift_id = $data->shift_id;
    if (signUpForShift($conn, $user_id, $shift_id)) {
        http_response_code(201); // Created
        echo json_encode(['success' => true, 'message' => 'Запись на смену успешно выполнена']);
    } 
}
$conn->close();
