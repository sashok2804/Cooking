<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка role_id перед получением информации о сменах
if ($role_id ==4) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Ошибка авторизации']);
    exit();
}

// Получение ID смены из запроса
$shift_id = isset($_GET['shift_id']) ? $_GET['shift_id'] : null;

// Подготовка SQL-запроса для получения информации о текущей смене и списка людей на смене
$query = "
    SELECT Shifts.id, Shifts.start_datetime, Shifts.end_datetime,
           Users.name, Users.email, Roles.title
    FROM Shifts
    LEFT JOIN Shift_Participation ON Shifts.id = Shift_Participation.shift_id
    LEFT JOIN Users ON Shift_Participation.user_id = Users.id
    LEFT JOIN Roles ON Users.role_id = Roles.id
    WHERE Shifts.id = ?
";

// Подготовка SQL-запроса для получения списка людей, которые были на смене
$participants_query = "
    SELECT Users.name, Users.email, Roles.title
    FROM Shift_Participation
    LEFT JOIN Users ON Shift_Participation.user_id = Users.id
    LEFT JOIN Roles ON Users.role_id = Roles.id
    WHERE Shift_Participation.shift_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $shift_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $shift_details = [];
    $participants = [];

    // Получение информации о текущей смене
    while ($row = $result->fetch_assoc()) {
        $shift_details[] = [
            'shift_id' => $row['id'],
            'start_datetime' => $row['start_datetime'],
            'end_datetime' => $row['end_datetime'],
            'employee_name' => $row['name'],
            'employee_email' => $row['email'],
            'employee_role' => $row['title'],
        ];
    }

    // Получение списка людей, которые были на смене
    if ($participants_stmt = $conn->prepare($participants_query)) {
        $participants_stmt->bind_param('i', $shift_id);
        $participants_stmt->execute();
        $participants_result = $participants_stmt->get_result();

        while ($participant_row = $participants_result->fetch_assoc()) {
            $participants[] = [
                'employee_name' => $participant_row['name'],
                'employee_email' => $participant_row['email'],
                'role_title' => $participant_row['title']
            ];
        }

        $participants_stmt->close();
    }

    $stmt->close();

    if (count($shift_details) >0) {
        http_response_code(200); // OK
        echo json_encode(['success' => true, 'shift_details' => $shift_details, 'participants' => $participants]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Информация о смене не найдена']);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Ошибка при выполнении запроса']);
}
$conn->close();
