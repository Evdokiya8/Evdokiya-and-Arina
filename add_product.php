<?php
session_start();
require 'database.php'; 

// Проверка на авторизацию пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_link = $_POST['image_link'];

    // Подготовка SQL-запроса для вставки товара
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_link) VALUES (?, ?, ?, ?)");
    
    // Исправление типа привязки: s - строка, d - дробное число
    $stmt->bind_param("ssds", $name, $description, $price, $image_link);
    
    if ($stmt->execute()) {
        echo "<script>alert('Товар добавлен!');</script>";
      
        header("Location: add_product.php"); // Перезагрузка страницы для отображения нового товара
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить товар</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>

<header>
    <h1>Администратор - Магазин мебели</h1>
    <nav>
        <ul>
            <li><a href="admin.php">Личный кабинет</a></li>
            <li><a href="#orders">Заявки</a></li>
            <li><a href="#products">Продукты</a></li>
            <li><a href="add_product.php">Добавить товар</a></li>
            <li><a href="main.php">Главная страница для пользователя</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>Добавить товар</h2>
    <form action="" method="post"> <!-- Изменено на пустую строку для отправки на тот же файл -->
        <div class="form-group">
            <label>Название товара</label>
            <input type="text" name="name" class="form-control" required />
        </div>
        <div class="form-group">
            <label>Описание товара</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label>Цена товара</label>
            <input type="number" step=".01" name="price" class="form-control" required />
        </div>
        <div class="form-group">
            <label>Ссылка на картинку</label>
            <input type="text" name="image_link" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary">Добавить товар</button>
       <?php if (isset($_SESSION['user_id'])): ?>
           <a href='logout.php' class='btn btn-danger'>Выйти</a><br><br><?php endif; ?>
       
    </form>

   <h2 class="mt-5">Все товары</h2>
   <div class="products-container row">
       <?php  
     
       $SQL = "SELECT id, description, name, image_link, price FROM products"; 
       $result = mysqli_query($conn, $SQL);  
       if (!$result) { 
           die("Couldn't execute query: " . mysqli_error($conn)); 
       } 

       if (mysqli_num_rows($result) > 0) { 
           while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { 
               echo "<div class='product col-md-4 mb-4'> 
                       <div class='card'>
                           <img src='{$row['image_link']}' alt='{$row['name']}' class='card-img-top'> 
                           <div class='card-body'>
                               <h5 class='card-title'>{$row['name']}</h5> 
                               <p class='card-text'>Цена: {$row['price']} руб.</p> 
                               <p>{$row['description']}</p> 
                           </div>
                       </div>
                     </div>"; 
           } 
       } else { 
           echo "<p>Товары не найдены.</p>"; 
       } 

       mysqli_close($conn); 
       ?> 
   </div>

   <!-- Обработка выхода -->
   <?php 
   if (isset($_GET['logout'])) {
       session_destroy(); 
       header('Location: login.php'); 
   }
   ?>
   
</div>

<footer> 
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p> 
</footer>

<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script> 
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script> 
<script src='https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js'></script>

</body>

</html>
