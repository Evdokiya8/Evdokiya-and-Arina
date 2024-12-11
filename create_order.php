<?php
require 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];
    $totalPrice = $_POST['total_price']; 

    $sql = "INSERT INTO orders (product_id, id_clients, total_price, order_date, order_status) VALUES (?, ?, ?, NOW(), 'В обработке')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iid", $productId, $userId, $totalPrice);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Товар успешно добавлен в заказ!";
    } else {
        $_SESSION['error_message'] = "Ошибка при добавлении товара: " . $conn->error;
    }

    $stmt->close();
    header("Location: view_orders.php"); 
    exit();
}
?>
