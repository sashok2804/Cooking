<?php
include('../db.php'); // Подключение к базе данных
include('../Users/GetToken.php'); // Подключение файла аутентификации

// Функция для очистки корзины
function clearCart($conn, $user_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Функция для создания нового заказа и его элементов
function createOrder($conn, $user_id) {
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, status_id, created_at) VALUES (?, 1, NOW())");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Получение ID только что созданного заказа
        $order_id = $conn->insert_id;
        $stmt->close();

        if ($order_id == 0) {
            throw new Exception('Ошибка при создании заказа');
        }

        // Перенос элементов из корзины в заказ
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) SELECT ?, product_id, quantity FROM cart WHERE user_id = ?");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        
        // Проверка наличия добавленных элементов заказа
        if ($stmt->affected_rows == 0 && $conn->query("SELECT COUNT(*) FROM cart WHERE user_id = $user_id")->fetch_column() > 0) {
            throw new Exception('Ошибка при добавлении элементов заказа');
        }
        $stmt->close();

        // Очистка корзины
        clearCart($conn, $user_id);

        // Подтверждение транзакции
        $conn->commit();

        return ['success' => true, 'order_id' => $order_id];
    } catch (Exception $e) {
        // Откат транзакции в случае ошибки
        $conn->rollback();
        
        // Логирование исключения
        error_log($e->getMessage());
        
        return ['success' => false, 'error' => $e->getMessage()];
    } finally {
        // Закрытие соединения с базой данных, если оно открыто
        if ($conn->ping()) {
            $conn->close();
        }
    }
}

// Получение токена из заголовка
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Токен не предоставлен']);
    exit;
}

// Извлечение токена из заголовка
list($token_type, $token) = explode(' ', $headers['Authorization'], 2);

if (!$user_id) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Аутентификация не удалась']);
    exit;
}

// Создание заказа
$result = createOrder($conn, $user_id);

// Проверка результата создания заказа
if (!$result['success']) {
    http_response_code(500); // Internal Server Error
    echo json_encode($result);
} else {
    http_response_code(201); // Created
    echo json_encode(['success' => true, 'message' => 'Заказ успешно создан', 'order_id' => $result['order_id']]);
}
