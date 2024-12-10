<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("si", $newStatus, $orderId);
        
        if ($stmt->execute()) {
            echo "<script>alert('Статус заказа обновлён!'); window.location.href='main_admin.php';</script>";
        } else {
            echo "<script>alert('Ошибка при обновлении статуса: " . mysqli_error($conn) . "'); window.location.href='main_admin.php';</script>";
        }
    } else {
        echo "<script>alert('Ошибка при подготовке запроса: " . mysqli_error($conn) . "'); window.location.href='main_admin.php';</script>";
    }
} else {
    header("Location: main_admin.php");
}
?>
