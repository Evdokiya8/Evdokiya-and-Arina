<?php
require('database.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    // Получение информации о товаре из обеих таблиц
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? UNION SELECT * FROM goods WHERE id = ?");
    
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . mysqli_error($conn));
    }

    $stmt->bind_param("ii", $productId, $productId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        
        // Форма редактирования товара
        echo "<h2>Редактировать товар: " . htmlspecialchars($product['name']) . "</h2>";
        echo "<form method='post' action='update_product.php'>";
        echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product['id']) . "'>";
        echo "<label for='name'>Название:</label><br>";
        echo "<input type='text' name='name' value='" . htmlspecialchars($product['name']) . "' required><br>";
        echo "<label for='price'>Цена:</label><br>";
        echo "<input type='number' name='price' value='" . htmlspecialchars($product['price']) . "' required><br>";
        // Добавьте другие поля по необходимости
        echo "<button type='submit'>Сохранить изменения</button>";
        echo "</form>";
        
    } else {
        echo "Товар не найден.";
    }
    
} else {
    echo "Некорректный запрос.";
}

$conn->close();
?>
