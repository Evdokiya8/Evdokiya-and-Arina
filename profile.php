<?php
require('database.php');

session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: profile.php");
    exit();
}

$clientId = $_SESSION['client_id'];
$sql = "SELECT id, fio, email, phone, password, delivery_address FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clientId);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $fio = $_POST['fio'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $delivery_address = $_POST['delivery_address'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateSql = "UPDATE clients SET fio=?, email=?, phone=?, delivery_address=?, password=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssssi", $fio, $email, $phone, $delivery_address, $hashedPassword, $clientId);
    } else {
        $updateSql = "UPDATE clients SET fio=?, email=?, phone=?, delivery_address=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssi", $fio, $email, $phone, $delivery_address, $clientId);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Данные успешно обновлены!');</script>";

        $_SESSION['client_fio'] = $fio;
    } else {
        echo "<script>alert('Ошибка при обновлении данных: " . mysqli_error($conn) . "');</script>";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Личный кабинет</h1>
        <nav>
            <ul>
                <li><a href="my_project/register.html">Личный кабинет</a></li>
                <li><a href="#orders">Мои заказы</a></li>
                <li><a href="#about">О Нас</a></li>
                <li><a href="#contact">Контакты</a></li>
            </ul>
        </nav>
    </header>

    <section class="banner">
        <h2>Добро пожаловать в ваш личный кабинет!</h2>
        <p>Здесь вы можете изменить свои данные профиля.</p>
    </section>

    <section id="profile">
        <h2>Изменение профиля</h2>
        <form method="post" action="">
            <div class="form-group mb-3">
                <label for="fio">ФИО</label>
                <input type="text" class="form-control" id="fio" name="fio" value="<?php echo htmlspecialchars($client['fio']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="phone">Телефон</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($client['phone']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="delivery_address">Адрес доставки</label>
                <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="<?php echo htmlspecialchars($client['delivery_address']); ?>" required>
                </div>
            <div class="form-group mb-3">
                <label for="password">Пароль (оставьте пустым для сохранения текущего)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </section>

    <footer>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

</body>
</html>
