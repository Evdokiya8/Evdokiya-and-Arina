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

// Обработка нового заказа
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    
    $productSQL = "SELECT price FROM goods WHERE id = ?";
    $stmt = $conn->prepare($productSQL);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $productResult = $stmt->get_result();
    
    if ($productResult->num_rows === 1) {
        $product = $productResult->fetch_assoc();
        $totalPrice = $product['price'];
        
        // Вставка нового заказа в базу данных
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fullName = $_POST['full_name'];
            $deliveryAddress = $_POST['delivery_address'];
            $paymentMethod = $_POST['payment_method'];
            $orderDate = date('Y-m-d H:i:s');
            $orderStatus = 'pending';

            $stmt = $conn->prepare("INSERT INTO orders (id_clients, product_id, order_date, order_status, delivery_address, full_name, payment_method, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                // Добавляем поле total_price в базу данных
                $stmt->bind_param("iissssss", $userId, $productId, $orderDate, $orderStatus, $deliveryAddress, $fullName, $paymentMethod, $totalPrice);

                if ($stmt->execute()) {
                    echo "<script>alert('Заказ успешно оформлен!');</script>";
                    header("Location: /my_project/php/main.php"); 
                    exit();
                } else {
                    echo "<script>alert('Ошибка при оформлении заказа: " . mysqli_error($conn) . "');</script>";
                }
            } else {
                echo "<script>alert('Ошибка при подготовке запроса: " . mysqli_error($conn) . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Продукт не найден.');</script>";
    }
}

// Получение всех продуктов для отображения из таблицы goods
$productSQL = "SELECT id, name, price FROM goods WHERE 1";
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

    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }

        h2 {
            margin-bottom: 20px; 
            font-size: 1.8rem; 
            color: #333; 
            text-align: center; 
        }

        .container {
            max-width: 600px; 
            margin: 30px auto; 
            padding: 30px; 
            background-color: white;
            border-radius: 10px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px; 
        }

        .form-group label {
            font-weight: bold; 
            color: #555; 
        }

        .form-control {
            border-radius: 5px; 
            border: 1px solid #ccc; 
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #007bff; 
            border-color: #007bff;
            width: 100%;
            border-radius: 5px; 
        }

        .btn-primary:hover {
            background-color: #0056b3; 
            border-color: #0056b3; 
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
        }
        
    </style>
</head>
<body>

<header class="bg-light p-3">
  <h1>Магазин мебели</h1>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="/my_project/php/profile.php">Личный кабинет</a></li>
          <li class="nav-item"><a class="nav-link" href="/my_project/php/view_orders.php">Мои заявки</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
      </ul>
  </nav>
</header>

<div class="container mt-5">
    <h2>Оформление заказа</h2>

    <form method="post" action="">
        <!-- Скрытое поле для передачи ID продукта -->
        <?php if (isset($_POST['product_id'])): ?>
            <input type='hidden' name='product_id' value='<?php echo htmlspecialchars($_POST['product_id']); ?>'>
        <?php endif; ?>

        <div class="form-group mb-3">
            <label for="full_name">ФИО:</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required placeholder="<?php echo htmlspecialchars($user['username']); ?>">
        </div>

        <div class="form-group mb-3">
            <label for="delivery_address">Адрес доставки:</label>
            <input type="text" class="form-control" id="delivery_address" name="delivery_address" required placeholder="Введите адрес доставки">
        </div>

        <?php if (isset($totalPrice)): ?>
            <div class="form-group mb-3">
                <label for="">Итоговая сумма:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($totalPrice); ?> руб." readonly>
            </div>

            <div class="form-group mb-3">
                <label for="">Способ оплаты:</label>
                <select name="payment_method" id="" class='form-select' required>
                    <option value="">Выберите способ оплаты</option>
                    <option value='card'>Банковская карта</option>
                    <option value='cash'>Наличные при доставке</option>
                    <!-- Добавьте другие способы оплаты по необходимости -->
                </select>
            </div>

            <!-- Кнопка оформления заказа -->
            <button type='submit' class='btn btn-primary mt-3'>Оформить заказ</button>

        <?php endif; ?>
        
    </div>
    </form>

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
