<?php
session_start();
require 'database.php';

// Проверка на авторизацию пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $deliveryAddress = $_POST['delivery_address'];
    $email = $_POST['email'];
    $paymentMethod = $_POST['payment_method'];

    // Получение информации о товаре
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ? UNION SELECT price FROM goods WHERE id = ?");
    $stmt->bind_param("ii", $productId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        $totalPrice = $product['price'] * $quantity;

        // Вставка заказа в новую таблицу customer_orders
        $stmt = $conn->prepare("INSERT INTO customer_orders (order_date, order_status, id_clients, product_id, delivery_address, payment_method, total_price) VALUES (NOW(), 'в обработке', ?, ?, ?, ?, ?)");
        $userId = $_SESSION['user_id']; // ID клиента из сессии
        $stmt->bind_param("iisss", $userId, $productId, $deliveryAddress, $paymentMethod, $totalPrice);

        if ($stmt->execute()) {
            echo "<script>alert('Заказ оформлен!'); window.location.href='main.php';</script>";
        } else {
            echo "<script>alert('Ошибка при оформлении заказа: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Товар не найден.');</script>";
    }
}

// Извлечение информации о товаре
$product = null;
if (isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    
    $stmt = $conn->prepare("SELECT id, name, description, price, image_link FROM products WHERE id = ? UNION SELECT id, name, description, price, image_link FROM goods WHERE id = ?");
    $stmt->bind_param("ii", $productId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-light p-3">
  <h1>Оформление заказа</h1>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="profile.php">Личный кабинет</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
      </ul>
  </nav>
</header>

<div class="container mt-5">
    <?php if ($product): ?>
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <img src="<?php echo htmlspecialchars($product['image_link']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid mb-3">
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <p><strong>Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</strong></p>

        <!-- Форма для оформления заказа -->
        <form method="post" action="">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <div class="mb-3">
                <label for="quantity" class="form-label">Количество:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="delivery_address" class="form-label">Адрес доставки:</label>
                <input type="text" id="delivery_address" name="delivery_address" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Электронная почта:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Способ оплаты:</label>
                <select id="payment_method" name="payment_method" required class='form-select'>
                    <option value="">Выберите способ оплаты</option>
                    <option value='наличные'>Наличные</option>
                    <option value='карта'>Карта</option>
                    <option value='электронные деньги'>Электронные деньги</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Оформить заказ</button>
        </form>

    <?php else: ?>
        <p>Товар не найден.</p>
    <?php endif; ?>

<!-- Footer -->
<footer class='mt-5'>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script> 

</body>
</html>
