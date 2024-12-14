<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка прав доступа
if ($role_id !== 1) { // Только администратор (role_id = 1) может создавать смены
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

// Обработка POST-запроса для создания смены
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Проверка входных данных
    if (empty($data['start_datetime']) || empty($data['end_datetime'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Дата и время начала и окончания обязательны']);
        exit();
    }

    $startDatetime = $data['start_datetime'];
    $endDatetime = $data['end_datetime'];

    // Проверка корректности дат
    if (strtotime($startDatetime) === false || strtotime($endDatetime) === false) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Неверный формат даты']);
        exit();
    }

    if (strtotime($startDatetime) >= strtotime($endDatetime)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Дата начала должна быть раньше даты окончания']);
        exit();
    }

    // Вставка новой смены в базу данных
    $query = "INSERT INTO Shifts (start_datetime, end_datetime) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Ошибка подготовки запроса']);
        exit();
    }

    $stmt->bind_param('ss', $startDatetime, $endDatetime);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(['success' => true, 'message' => 'Смена успешно создана']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Ошибка создания смены']);
    }

    $stmt->close();
}

// Закрытие соединения с базой данных
$conn->close();
