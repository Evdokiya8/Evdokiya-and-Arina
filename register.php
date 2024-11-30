<?php
require 'database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $phone = $_POST['phone'];
    $role = 'client'; 
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone) VALUES (?, ?, ?, ?, ?);");
    if( ! $stmt ){ //если ошибка - убиваем процесс и выводим сообщение об ошибке.
    	die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
    }
    $stmt->bind_param("sssss", $username, $email, $password, $role, $phone);
    
    if ($stmt->execute()) {
        //echo "Регистрация успешна!";
        session_start();
        $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: /golovkina.e.p/my_project/html/main.html"); 
       // header("Location: /golovkina.e.p/my_project/html/login.html");
        
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>
