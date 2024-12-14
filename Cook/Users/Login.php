<?php
require_once '../db.php';

function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($input['email']) ? sanitizeInput($input['email']) : null;
    $password = isset($input['password']) ? sanitizeInput($input['password']) : null;

    if ($email && $password) {
        $query = "SELECT id, email, password, role_id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    $newToken = bin2hex(random_bytes(16));
                    $updateQuery = "UPDATE users SET authentication_token = ? WHERE email = ?";
                    if ($updateStmt = $conn->prepare($updateQuery)) {
                        $updateStmt->bind_param("ss", $newToken, $email);
                        $updateStmt->execute();
                        $updateStmt->close();
                        echo json_encode([
                            'authentication_token' => $newToken,
                            'role_id' => $user['role_id']
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Ошибка сервера при обновлении токена']);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(['error' => 'Неверные учетные данные']);
                }
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Неверные учетные данные']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка сервера']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Email и пароль не могут быть пустыми']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается']);
}
