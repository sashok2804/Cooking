<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка role_id перед получением информации о сменах
if ($role_id != 1) {
    http_response_code(403); // Forbidden
    $errorMessage = 'Ошибка авторизации';
    error_log($errorMessage);
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit();
}

// Получение информации о сотруднике
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $userId = $_GET['id'];

        // Получение информации о пользователе
        $userQuery = "
            SELECT Users.id, Users.name, Users.email, Roles.title AS role_title
            FROM Users
            INNER JOIN Roles ON Users.role_id = Roles.id
            WHERE Users.id = ?
        ";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param('i', $userId);
        if (!$userStmt->execute()) {
            $errorMessage = "Ошибка выполнения запроса: " . $userStmt->error;
            error_log($errorMessage);
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            $userStmt->close();
            $conn->close();
            exit();
        }
        $userResult = $userStmt->get_result();

        if ($userResult->num_rows > 0) {
            $userRow = $userResult->fetch_assoc();

            // Получение информации о сменах пользователя
            $shiftsQuery = "
                SELECT Shifts.id, Shifts.start_datetime, Shifts.end_datetime
                FROM Shifts
                INNER JOIN Shift_Participation ON Shifts.id = Shift_Participation.shift_id
                WHERE Shift_Participation.user_id = ?
                ORDER BY Shifts.start_datetime
            ";
            $shiftsStmt = $conn->prepare($shiftsQuery);
            $shiftsStmt->bind_param('i', $userId);
            if (!$shiftsStmt->execute()) {
                $errorMessage = "Ошибка выполнения запроса: " . $shiftsStmt->error;
                error_log($errorMessage);
                http_response_code(500); // Internal Server Error
                echo json_encode(['success' => false, 'message' => $errorMessage]);
                $userStmt->close();
                $shiftsStmt->close();
                $conn->close();
                exit();
            }
            $shiftsResult = $shiftsStmt->get_result();

            $userShifts = [];
            while ($shiftRow = $shiftsResult->fetch_assoc()) {
                $userShifts[] = $shiftRow;
            }

            $response = [
                'success' => true,
                'user' => $userRow,
                'shifts' => $userShifts
            ];

            http_response_code(200); // OK
            echo json_encode($response);
        } else {
            http_response_code(404); // Not Found
            $errorMessage = 'Сотрудник не найден';
            error_log($errorMessage);
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        }

        $userStmt->close();
        $shiftsStmt->close();
    } else {
        http_response_code(400); // Bad Request
        $errorMessage = 'Параметр id не передан';
        error_log($errorMessage);
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }
}
$conn->close();
