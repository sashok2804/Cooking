<?php

include('../db.php');
include('../Users/GetToken.php');

// После успешной аутентификации и получения user_id, извлекаем товары из корзины
$query = "SELECT Cart.id, Cart.product_id, Cart.quantity, Products.title, Products.price, Products.image_path, 
          (Cart.quantity * Products.price) AS total_price
          FROM Cart
          JOIN Products ON Cart.product_id = Products.id
          WHERE Cart.user_id = ?";

if ($stmt = $conn->prepare($query)) {
    // Привязываем user_id к запросу
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = [];

    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }

    // Проверяем, есть ли товары в корзине
    if (count($cartItems) > 0) {
        $response["success"] = true;
        $response["cart_items"] = $cartItems;
    } else {
        $response["success"] = false;
        $response["message"] = "Корзина пуста";
    }
    $stmt->close();
} else {
    $response["success"] = false;
    $response["message"] = "Ошибка при подготовке запроса: " . $conn->error;
}

echo json_encode($response);
$conn->close();
