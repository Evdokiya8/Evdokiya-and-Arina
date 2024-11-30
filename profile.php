<?php 
// Включаем отображение ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключаем файл с соединением к базе данных
require('database.php'); // Убедитесь, что путь к файлу правильный

// Проверяем, авторизован ли пользователь (предполагается, что у вас есть система аутентификации)
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Перенаправляем на страницу входа, если пользователь не авторизован
    exit();
}

// Получаем текущие данные пользователя из базы данных
$user_id = $_SESSION['user_id'];
$sql = "SELECT `full_name`, `address`, `phone`, `password` FROM `users` WHERE `id` = '$user_id'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("Ошибка получения данных пользователя: " . mysqli_error($conn));
}

// Обработка формы при отправке
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];

    // Проверка и обновление пароля
    if (!empty($password)) {
        // Хешируем новый пароль
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE `users` SET `full_name`='$full_name', `address`='$address', `phone`='$phone', `password`='$hashed_password' WHERE `id`='$user_id'";
    } else {
        // Если пароль не изменяется
        $update_sql = "UPDATE `users` SET `full_name`='$full_name', `address`='$address', `phone`='$phone' WHERE `id`='$user_id'";
    }

    if (mysqli_query($conn, $update_sql)) {
        echo "<p>Данные успешно обновлены!</p>";
        // Обновляем данные в сессии (если нужно)
        $_SESSION['full_name'] = $full_name;
    } else {
        echo "Ошибка обновления данных: " . mysqli_error($conn);
    }
}

// Закрываем соединение с базой данных
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение профиля</title>
</head>
<body>
    <h1>Изменение профиля</h1>
    <form method="post" action="">
        <label for="full_name">ФИО:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required><br>

        <label for="address">Адрес:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required><br>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>

        <label for="password">Новый пароль (оставьте пустым, если не хотите менять):</label>
        <input type="password" id="password" name="password"><br>

        <button type="submit">Сохранить изменения</button>
    </form>

    <footer>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
    </footer>
</body>
</html>