<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

// Получение данных текущего администратора из базы данных
$userId = $_SESSION['user_id'];
$sql = "SELECT username, email, phone FROM users WHERE id = ?";
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

// Обработка формы обновления данных
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
        // Если пароль не изменен
        $updateSql = "UPDATE users SET username=?, email=?, phone=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssi", $username, $email, $phone, $userId);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Данные успешно обновлены!');</script>";
        $_SESSION['username'] = $username; // Обновляем имя пользователя в сессии
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
    <link rel="stylesheet" href="styles.css">
    <title>Личный кабинет администратора</title>

    <style>
        body {
            background-color: #f4f7fa; /* Светлый фон страницы */
            font-family: 'Arial', sans-serif; /* Шрифт страницы */
        }

        h2 {
            margin-bottom: 20px; /* Отступ снизу для заголовка */
            font-size: 1.8rem; /* Размер шрифта заголовка */
            color: #333; /* Цвет текста заголовка */
            text-align: center; /* Центрирование заголовка */
        }

        .container {
            max-width: 600px; /* Максимальная ширина контейнера */
            margin: 30px auto; /* Центрирование контейнера с отступом сверху и снизу */
            padding: 30px; /* Внутренние отступы контейнера */
            background-color: white; /* Белый фон для контейнера */
            border-radius: 10px; /* Закругленные углы контейнера */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Легкая тень для контейнера */
        }

        .form-group {
            margin-bottom: 15px; /* Отступ между полями ввода */
        }

        .form-group label {
            font-weight: bold; /* Жирный текст для меток */
            color: #555; /* Цвет меток */
        }

        .form-control {
            border-radius: 5px; /* Закругленные углы полей ввода */
            border: 1px solid #ccc; /* Цвет границы полей ввода */
            transition: border-color 0.3s; /* Плавный переход цвета границы */
        }

        .form-control:focus {
            border-color: #007bff; /* Цвет границы при фокусе */
            box-shadow: none; /* Убираем обводку при фокусе */
        }

        .btn-primary {
            background-color: #737383; /* Цвет кнопки */
            border-color: #737383; /* Цвет границы кнопки */
            width: 100%; /* Кнопка на всю ширину */
            border-radius: 5px; /* Закругленные углы кнопки */
        }

        .btn-primary:hover {
            background-color: #9898ab; /* Цвет кнопки при наведении */
            border-color: #9898ab; /* Цвет границы кнопки при наведении */
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
            <li class="nav-item"><a class="nav-link" href="main_admin.php">На главную</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Выйти</a></li> <!-- Ссылка на выход -->
        </ul>
    </nav>
</header>

<div class="container mt-5">
    <h2>Профиль администратора</h2>

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
            <label for="password">Пароль </label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </form>

  

</div>
<footer class='mt-5'>
    <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>
<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script> 
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script> 
<script src='https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js'></script>

</body>
</html>
