<?php
session_start();
require 'database.php';

// Проверка на авторизацию администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Обработка удаления заявки
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order_id'])) {
    $deleteOrderId = $_POST['delete_order_id'];

    // Удаление заказа из базы данных
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $deleteOrderId);
    
    if ($stmt->execute()) {
        echo "<script>alert('Заявка успешно удалена!');</script>";
    } else {
        echo "<script>alert('Ошибка при удалении заявки: " . mysqli_error($conn) . "');</script>";
    }
}

// Обработка изменения статуса заявки
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    // Обновление статуса заказа в базе данных
    $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("si", $newStatus, $orderId);
        
        if ($stmt->execute()) {
            echo "<script>alert('Статус заявки обновлён!');</script>";
        } else {
            echo "<script>alert('Ошибка при обновлении статуса: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Ошибка при подготовке запроса: " . mysqli_error($conn) . "');</script>";
    }
}

// Поиск и фильтрация данных
$searchQuery = '';
$statusFilter = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}
if (isset($_GET['status_filter'])) {
    $statusFilter = $_GET['status_filter'];
}

$sql = "SELECT id, order_date, order_status, id_clients, delivery_address, payment_method, total_price FROM orders 
        WHERE (delivery_address LIKE ? OR id_clients LIKE ?)";
        
if ($statusFilter !== '') {
    $sql .= " AND order_status = ?";
}

$stmt = $conn->prepare($sql);
$likeQuery = "%" . $searchQuery . "%";
if ($statusFilter !== '') {
    $stmt->bind_param("sss", $likeQuery, $likeQuery, $statusFilter);
} else {
    $stmt->bind_param("ss", $likeQuery, $likeQuery);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заявками</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Стили для формы поиска */
        .input-group input[type="text"] {
            border: 1px solid #ccc; /* Цвет границы */
            border-radius: 4px; /* Закругленные углы */
            padding: 10px; /* Внутренние отступы */
            transition: border-color 0.3s; /* Плавный переход цвета границы */
        }

        .input-group input[type="text"]:focus {
            border-color: #007bff; /* Цвет границы при фокусе */
            outline: none; /* Убираем обводку при фокусе */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Легкая тень при фокусе */
        }

        .input-group select {
            border: 1px solid #ccc; /* Цвет границы */
            border-radius: 4px; /* Закругленные углы */
            padding: 10px; /* Внутренние отступы */
        }

        .table {
            margin-top: 20px; /* Отступ сверху для таблицы */
        }

        .table th, .table td {
            text-align: center; /* Выравнивание текста по центру */
            vertical-align: middle; /* Выравнивание по вертикали */
        }

        .table th {
            background-color: #007bff; /* Цвет фона заголовков таблицы */
            color: white; /* Цвет текста заголовков таблицы */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2; /* Цвет фона для нечетных строк таблицы */
        }
        
        .btn-danger {
            margin-left: 5px; /* Отступ между кнопками удаления и выбора статуса */
        }
    </style>
</head>
<body>

<header class="bg-light p-3">
  <h1>Администрация - Управление заявками</h1>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <ul class="navbar-nav">
      	<li class="nav-item"><a class="nav-link" href="main_admin.php">На главную</a></li>
          <li class="nav-item"><a class="nav-link" href="admin.php">Личный кабинет</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Выйти</a></li>
      </ul>
  </nav>
</header>

<div class="container mt-5">
    <h2>Заявки</h2>

    
    <form method="get" action="" class="mb-4">
        <div class="input-group mb-3">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control" placeholder="Поиск по адресу доставки или ID клиента">
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

   
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата заказа</th>
                <th>Статус заказа</th>
                <th>ID клиента</th>
                <th>Адрес доставки</th>
                <th>Метод оплаты</th>
                <th>Итого</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr data-id="<?php echo htmlspecialchars($row['id']); ?>">
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['id_clients']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?> руб.</td>
                        <td>
                            
                            <form method='post' action='' style='display:inline;'>
                                <input type='hidden' name='order_id' value='<?php echo htmlspecialchars($row['id']); ?>'>
                                <select name='new_status' onchange='this.form.submit()' class='form-select'>
                                    <option value=''>Изменить статус</option>
                                    <option value='в обработке'>В обработке</option>
                                    <option value='в пути'>В пути</option>
                                    <option value='доставлено'>Доставлено</option>
                                </select>
                            </form>

                            <!-- Форма для удаления заявки -->
                            <form method='post' action='' style='display:inline;'>
                                <input type='hidden' name='delete_order_id' value='<?php echo htmlspecialchars($row['id']); ?>'>
                                <button type='submit' class='btn btn-danger btn-sm'>Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan='8'>Заявки не найдены.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<footer class='mt-5'>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>

<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script> 
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script> 
<script src='https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js'></script>

<?php
$conn->close();
?>
