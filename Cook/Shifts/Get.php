<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка role_id перед получением информации о сменах
if ($role_id == 4) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Ошибка авторизации']);
    exit();
}

// Функция для получения всех смен и ID смен, на которые записан пользователь
function getShiftsAndUserShifts($conn, $userId, $isAdmin) {
    $baseQuery = "
        SELECT shifts.id, shifts.start_datetime, shifts.end_datetime,
               shift_participation.user_id IS NOT NULL as is_user_signed_up
        FROM Shifts AS shifts
        LEFT JOIN Shift_Participation AS shift_participation
        ON shifts.id = shift_participation.shift_id AND shift_participation.user_id = ?
    ";

    if ($isAdmin) {
        $query = $baseQuery . " ORDER BY shifts.start_datetime";
    } else {
        $query = $baseQuery . " WHERE shifts.end_datetime > NOW() ORDER BY shifts.start_datetime";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $shifts = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $shifts[] = $row;
        }
    }
    return $shifts;
}

// Обработка GET запроса для получения смен
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $isAdmin = ($role_id === 1); // Проверка, является ли пользователь администратором
    $shifts = getShiftsAndUserShifts($conn, $user_id, $isAdmin);
    if (empty($shifts)) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Смены не найдены']);
    } else {
        http_response_code(200); // OK
        echo json_encode(['success' => true, 'shifts' => $shifts]);
    }
}

// Закрытие соединения с базой данных
$conn->close();
