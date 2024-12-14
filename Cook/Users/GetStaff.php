<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Получение токена, user_id и role_id

// Проверка role_id перед получением информации о сменах
if ($role_id != 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Ошибка авторизации']);
    exit();
}

$query = "
    SELECT Users.id, Users.name, Users.email, Roles.title AS role_title
    FROM Users
    INNER JOIN Roles ON Users.role_id = Roles.id
    WHERE role_id <> 4
";

$stmt = $conn->prepare($query);

$stmt->execute();
$result = $stmt->get_result();

$employees = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'role_title' => $row['role_title']
        ];
    }

    http_response_code(200); // OK
    echo json_encode(['success' => true, 'employees' => $employees]);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['success' => false, 'message' => 'Сотрудники не найдены']);
}

$stmt->close();
$conn->close();
