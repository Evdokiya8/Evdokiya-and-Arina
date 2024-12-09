<?php
require 'database.php';
session_start();

// Проверка на авторизацию пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Перенаправление на страницу входа, если не авторизован
    exit();
}

// Получение заказов текущего пользователя из базы данных
$userId = $_SESSION['user_id'];
$sql = "SELECT id, order_date, order_status FROM orders WHERE id_clients = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="bg-light p-3">
  <h1>Магазин мебели</h1>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="profile.php">Личный кабинет</a></li>
          <li class="nav-item"><a class="nav-link" href="create_order.php">Создать заявку</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">О Нас</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
      </ul>
  </nav>
</header>

<div class="container mt-5">
    <h2>Мои заказы</h2>

    <!-- Таблица заказов -->
    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Дата заказа</th><th>Статус</th></tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr><td><?php echo $row['id']; ?></td><td><?php echo htmlspecialchars($row['order_date']); ?></td><td><?php echo htmlspecialchars($row['order_status']); ?></td></tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan='3'>Заказы не найдены.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<!-- Footer -->
<footer class='mt-5'>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>

</div>

<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script> 
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script> 
<script src='https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js'></script>

</body>
</html>