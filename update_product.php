<?php
require('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Обновление информации о товаре в таблице products
    $stmt = $conn->prepare("UPDATE products SET name=?, price=? WHERE id=?");
    
    if ($stmt === false) {
        die("Ошибка подготовки запроса к products: " . mysqli_error($conn));
    }

    $stmt->bind_param("ssi", $name, $price, $productId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            $stmt->close(); 

            // Обновление информации о товаре в таблице goods
            $stmt = $conn->prepare("UPDATE goods SET name=?, price=? WHERE id=?");
            if ($stmt === false) {
                die("Ошибка подготовки запроса к goods: " . mysqli_error($conn));
            }

            $stmt->bind_param("ssi", $name, $price, $productId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Товар успешно обновлен в goods!'); window.location.href = 'main_admin.php';</script>";
                } else {
                    echo "<script>alert('Товар не найден в goods.'); window.location.href = 'main_admin.php';</script>";
                }
            } else {
                echo "<script>alert('Ошибка при обновлении товара в goods: " . mysqli_error($conn) . "'); window.location.href = 'main_admin.php';</script>";
            }
        } else {
            echo "<script>alert('Товар успешно обновлен в products!'); window.location.href = 'main_admin.php';</script>";
        }
    } else {
        echo "<script>alert('Ошибка при обновлении товара: " . mysqli_error($conn) . "'); window.location.href = 'main_admin.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
