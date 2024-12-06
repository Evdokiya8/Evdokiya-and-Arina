<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}


$userId = $_SESSION['user_id'];
$sql = "SELECT username, email, phone, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "Пользователь не найден.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateSql = "UPDATE users SET username=?, email=?, phone=?, password=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssi", $username, $email, $phone, $hashedPassword, $userId);
    } else {
        $updateSql = "UPDATE users SET username=?, email=?, phone=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssi", $username, $email, $phone, $userId);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Данные успешно обновлены!');</script>";
        $_SESSION['username'] = $username;
    } else {
        echo "<script>alert('Ошибка при обновлении данных: " . mysqli_error($conn) . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
<header>
    <h1>Магазин мебели</h1>
    <nav>
        <ul>
            <li><a href="/my_project/php/profile.php">Личный кабинет</a></li>
            <li><a href="#products">Продукты</a></li>
            <li><a href="#about">О Нас</a></li>
            <li><a href="#contact">Контакты</a></li>
        </ul>
    </nav>
</header>

<div class="container mt-5">
    <h2>Профиль пользователя</h2>

    <form method="post" action="">
        <div class="form-group mb-3">
            <label for="username">Имя пользователя</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="email">Электронная почта</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="phone">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>
        <div class="form-group mb-3">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </form>

    <footer class="mt-5">
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
    </footer>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

</body>
</html>
