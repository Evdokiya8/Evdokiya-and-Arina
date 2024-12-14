<?php
session_start();
require 'database.php';

// Проверка на авторизацию пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Защита от CSRF
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Обработка удаления заявки
    if (isset($_POST['delete_order_id'])) {
        $deleteOrderId = intval($_POST['delete_order_id']);
        $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("i", $deleteOrderId);
            if ($stmt->execute()) {
                header("Location: manage_orders.php?message=Заказ удалён!");
                exit();
            } else {
                error_log("Ошибка при удалении заказа: " . mysqli_error($conn));
                header("Location: manage_orders.php?error=Ошибка при удалении заказа.");
                exit();
            }
        } else {
            error_log("Ошибка при подготовке запроса: " . mysqli_error($conn));
            header("Location: manage_orders.php?error=Ошибка при подготовке запроса.");
            exit();
        }
    }

    // Обработка изменения статуса заявки
    if (isset($_POST['update_order_id']) && isset($_POST['order_status'])) {
        $updateOrderId = intval($_POST['update_order_id']);
        $newStatus = $_POST['order_status'];
        $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("si", $newStatus, $updateOrderId);
            if ($stmt->execute()) {
                header("Location: manage_orders.php?message=Статус заказа обновлён!");
                exit();
            } else {
                error_log("Ошибка при обновлении статуса заказа: " . mysqli_error($conn));
                header("Location: manage_orders.php?error=Ошибка при обновлении статуса заказа.");
                exit();
            }
        } else {
            error_log("Ошибка при подготовке запроса: " . mysqli_error($conn));
            header("Location: manage_orders.php?error=Ошибка при подготовке запроса.");
            exit();
        }
    }
}

// Генерация CSRF-токена
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Поиск и фильтрация данных
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = '%' . $conn->real_escape_string($_GET['search']) . '%';
} else {
    $searchQuery = '%';
}

// Получение списка заказов для отображения с фильтрацией
$stmt = $conn->prepare("SELECT id, order_status FROM orders WHERE order_status LIKE ?");
$stmt->bind_param("s", $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заказами</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        form {
            display: inline;
        }
        input[type="text"], select {
            padding: 5px;
            margin-right: 10px;
        }
        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message, .error {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .message {
            background-color: #dff0d8; /* Light green */
            color: #3c763d; /* Dark green */
        }

>
.error {
            background-color: #f2dede; /* Light red */
            color: #a94442; /* Dark red */
        }
    </style>
</head>
<body>
<h2>Управление заказами</h2>

<?php if (isset($_GET['message'])) echo "<div class='message'>".$_GET['message']."</div>"; ?>
<?php if (isset($_GET['error'])) echo "<div class='error'>".$_GET['error']."</div>"; ?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Поиск по статусу" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    <button type="submit">Поиск</button>
</form>

<table>
    <tr><th>ID Заказа</th><th>Статус Заказа</th><th>Действия</th></tr>

<?php while ($order = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($order['id']); ?></td>
    <td>
        <?php echo htmlspecialchars($order['order_status']); ?>
        
        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="update_order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <select name="order_status">
                <option value="Новый" <?php if ($order['order_status'] == 'Новый') echo 'selected'; ?>>Новый</option>
                <option value="В обработке" <?php if ($order['order_status'] == 'В обработке') echo 'selected'; ?>>В обработке</option>
                <option value="Завершён" <?php if ($order['order_status'] == 'Завершён') echo 'selected'; ?>>Завершён</option>
                <option value="Отменён" <?php if ($order['order_status'] == 'Отменён') echo 'selected'; ?>>Отменён</option>
            </select>
            <button type="submit">Изменить статус</button>
        </form>
    </td>
    <td>
        
        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="delete_order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit">Удалить</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
