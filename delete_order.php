<?php
session_start();
require 'database.php';

// Проверка на авторизацию пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Обработка удаления заявки
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order_id'])) {
    $deleteOrderId = $_POST['delete_order_id'];

    // Удаление заказа из базы данных
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $deleteOrderId);
        
        if ($stmt->execute()) {
            echo "<script>alert('Заказ удалён!'); window.location.href='main_admin.php';</script>";
        } else {
            echo "<script>alert('Ошибка при удалении заказа: " . mysqli_error($conn) . "'); window.location.href='main_admin.php';</script>";
        }
    } else {
        echo "<script>alert('Ошибка при подготовке запроса: " . mysqli_error($conn) . "'); window.location.href='main_admin.php';</script>";
    }
} else {
    header("Location: main_admin.php");
}
?>