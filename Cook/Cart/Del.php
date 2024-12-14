<?php

include('../db.php');
include('../Users/GetToken.php');


// Проверка наличия id товара в запросе
if (!isset($data->id)) {
    http_response_code(400);
    $response["success"] = false;
    $response["message"] = "ID товара не предоставлен";
    echo json_encode($response);
    exit;
}

$id = $data->id;

$query = "DELETE FROM Cart WHERE user_id = ? AND id = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('ii', $user_id, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        http_response_code(200); 
        $response["success"] = true;
        $response["message"] = "Товар успешно удален из корзины";
    } else {
        http_response_code(404); 
        $response["success"] = false;
        $response["message"] = "Товар в корзине не найден";
    }
    $stmt->close();
} else {
    http_response_code(500);
    $response["success"] = false;
    $response["message"] = "Ошибка при подготовке запроса: " . $conn->error;
}

echo json_encode($response);
$conn->close();
