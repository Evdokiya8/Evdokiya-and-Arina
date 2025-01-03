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
        // Если пароль не изменен
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
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        }

        .form-group {
            margin-bottom: 15px; 
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
            <li class="nav-item"><a class="nav-link" href="main.php">На главную</a></li>
            <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
            <li class="nav-item"><a class="nav-link" href="#about">О Нас</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Выйти</a></li>
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
            <label for="password">Пароль </label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </div>
    </form>

<!-- Footer -->
<footer class='mt-5'>
    <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
</footer>



<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script> 
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script> 
<script src='https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js'></script>

</body>
</html>
