<?php
require('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    $stmt = $conn->prepare("DELETE FROM customer_orders WHERE product_id = ?");
    if ($stmt === false) {
        die("Ошибка подготовки запроса к customer_orders: " . mysqli_error($conn));
    }
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt === false) {
        die("Ошибка подготовки запроса к products: " . mysqli_error($conn));
    }
    $stmt->bind_param("i", $productId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            $stmt->close(); 

            $stmt = $conn->prepare("DELETE FROM goods WHERE id = ?");
            if ($stmt === false) {
                die("Ошибка подготовки запроса к goods: " . mysqli_error($conn));
            }
            $stmt->bind_param("i", $productId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Товар успешно удален из goods!'); window.location.href = 'main_admin.php';</script>";
                } else {
                    echo "<script>alert('Товар не найден в goods.'); window.location.href = 'main_admin.php';</script>";
                }
            } else {
                echo "<script>alert('Ошибка при удалении товара из goods: " . mysqli_error($conn) . "'); window.location.href = 'main_admin.php';</script>";
            }
        } else {
            echo "<script>alert('Товар успешно удален из products!'); window.location.href = 'main_admin.php';</script>";
        }
    } else {
        echo "<script>alert('Ошибка при удалении товара: " . mysqli_error($conn) . "'); window.location.href = 'main_admin.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
