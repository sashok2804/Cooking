<?php
include('../db.php');

$response = [];

// Установка заголовка Content-Type для JSON

try {
    // Запрос к базе данных для получения продуктов
    $sql = "SELECT * FROM Products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = [];

        // Получение всех продуктов
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        // Успешный ответ с данными продуктов
        http_response_code(200); // OK
        $response["success"] = true;
        $response["data"] = $products;
    } else {
        // Ошибка, если продукты не найдены
        http_response_code(404); // Not Found
        throw new Exception("No products found");
    }
} catch (Exception $e) {
    // Отправка кода ошибки сервера
    http_response_code(500); // Internal Server Error
    $response["success"] = false;
    $response["message"] = $e->getMessage();
} finally {
    // Закрытие соединения с базой данных
    $conn->close();
    // Отправка JSON ответа
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
