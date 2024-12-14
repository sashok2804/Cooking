<?php

include('../db.php');
include('../Users/GetToken.php');

try {
    if (isset($data->product_id, $data->quantity)) {
        $product_id = $data->product_id;
        $quantity = $data->quantity;

        // Подготовка запроса для проверки наличия товара в корзине
        $checkStmt = $conn->prepare("SELECT quantity FROM Cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->bind_param("ii", $user_id, $product_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $checkStmt->close();

        if ($result->num_rows > 0) {
            // Товар уже есть в корзине, обновляем количество
            $row = $result->fetch_assoc();
            $newQuantity = $row['quantity'] + $quantity;
            $updateStmt = $conn->prepare("UPDATE Cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $updateStmt->bind_param("iii", $newQuantity, $user_id, $product_id);
            $updateStmt->execute();
            $updateStmt->close();

            $response["success"] = true;
            $response["message"] = "Количество товара обновлено успешно.";
        } else {
            // Товара нет в корзине, добавляем новую запись
            $insertStmt = $conn->prepare("INSERT INTO Cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
            $insertStmt->execute();
            $insertStmt->close();

            $response["success"] = true;
            $response["message"] = "Товар добавлен в корзину успешно.";
        }
    } else {
        throw new Exception("Неполные данные.");
    }
} catch (mysqli_sql_exception $e) {
    $response["success"] = false;
    $response["message"] = "Ошибка базы данных: " . $e->getMessage();
} catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = $e->getMessage();
} finally {
    $conn->close();
    echo json_encode($response);
}
