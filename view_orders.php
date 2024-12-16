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
$searchQuery = '';
$statusFilter = '';

// Обработка поиска и фильтрации
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}
if (isset($_GET['status_filter'])) {
    $statusFilter = $_GET['status_filter'];
}

$sql = "SELECT id, order_date, order_status, delivery_address, payment_method, total_price 
        FROM customer_orders 
        WHERE id_clients = ?";

$conditions = [];
$params = [$userId];

if (!empty($searchQuery)) {
    $sql .= " AND (delivery_address LIKE ? OR order_status LIKE ?)";
    $conditions[] = "%" . $searchQuery . "%";
    $conditions[] = "%" . $searchQuery . "%";
}

if (!empty($statusFilter)) {
    $sql .= " AND order_status = ?";
    $conditions[] = $statusFilter;
}

$stmt = $conn->prepare($sql);
$params = array_merge($params, $conditions);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
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

    <!-- Форма поиска и фильтрации -->
    <form method="get" action="" class="mb-4">
        <div class="input-group mb-3">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control" placeholder="Поиск по адресу доставки или статусу">
            <button type="submit" class="btn btn-primary">Поиск</button>
        </div>

        <div class="input-group mb-3">
            <select name="status_filter" class="form-select" onchange='this.form.submit()'>
                <option value="">Все статусы</option>
                <option value="в обработке" <?php if ($statusFilter === 'в обработке') echo 'selected'; ?>>В обработке</option>
                <option value="в пути" <?php if ($statusFilter === 'в пути') echo 'selected'; ?>>В пути</option>
                <option value="доставлено" <?php if ($statusFilter === 'доставлено') echo 'selected'; ?>>Доставлено</option>
            </select>
        </div>
    </form>

    <!-- Таблица заказов -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата заказа</th>
                <th>Статус</th>
                <th>Адрес доставки</th>
                <th>Способ оплаты</th>
                <th>Итоговая сумма</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?> руб.</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan='6'>Заказы не найдены.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<!-- Footer -->
<footer class='mt-5'>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script> 

</body>
</html>
