<?php
session_start();
require 'database.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_link = $_POST['image_link'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_link) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $description, $price, $image_link);
    
    if ($stmt->execute()) {
        echo "Товар добавлен!";
        
        header("Location: /golovkina.e.p/my_project/html/add_product.html
        
        ");
        
      
      
      
      
      
      
      exit();
 
      } else {
          echo "Ошибка: " . $stmt->error;
      }
}
?>
