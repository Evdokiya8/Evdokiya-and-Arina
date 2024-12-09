<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $orderDate = date('Y-m-d H:i:s');
    $orderStatus = 'pending';

    $stmt = $conn->prepare("INSERT INTO orders (id_clients, product_id, order_date, order_status) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiss", $userId, $productId, $orderDate, $orderStatus);

        if ($stmt->execute()) {
            echo "<script>alert('Заказ успешно оформлен!');</script>";
        } else {
            echo "<script>alert('Ошибка при оформлении заказа: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Ошибка при подготовке запроса: " . mysqli_error($conn) . "');</script>";
    }
}

$productSQL = "SELECT `id`, `name`, `price` FROM `goods` WHERE 1";
$productResult = mysqli_query($conn, $productSQL);

if (!$productResult) {
    die("Ошибка при выполнении запроса: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-light p-3">
  <h1>Магазин мебели</h1>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="profile.php">Личный кабинет</a></li>
          <li class="nav-item"><a class="nav-link" href="view_orders.php">Мои заказы</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
      </ul>
  </nav>
</header>

<div class="container mt-5">
    <h2>Оформление заказа</h2>

    <!-- Форма для оформления заказа -->
    <form method="post" action="">
        <div class="form-group">
            <label for="product_id">Выберите продукт:</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <?php while ($row = mysqli_fetch_assoc($productResult)): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                        <?php echo htmlspecialchars($row['name']) . ' - ' . htmlspecialchars($row['price']) . ' руб.'; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Оформить заказ</button>
    </form>
        <h3 class="mt-5">Ваши заказы:</h3>
    <?php
    $ordersSQL = "SELECT o.id, o.order_date, o.order_status, g.name FROM orders o JOIN goods g ON o.product_id = g.id WHERE o.id_clients = ?";
    $stmt = $conn->prepare($ordersSQL);
    
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $ordersResult = $stmt->get_result();

        if ($ordersResult->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Продукт</th>
                        <th>Дата заказа</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['name']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>У вас нет заказов.</p>
        <?php endif; 
    } else {
        echo "Ошибка при подготовке запроса для получения заказов: " . mysqli_error($conn);
    }
    ?>
</div>

<footer class='mt-5'>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>

<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script> 
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script> 
<script src='https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js'></script>

</body>
</html>

<?php
$conn->close();
?>
