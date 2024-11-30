<?php
$servername = 'localhost';
$username = 'golovkina.e.p'; 
$password = '1284'; 
$dbname = 'golovkina.e.p';

$conn = mysqli_connect( $servername, $username, $password, $dbname);

if ($conn->connect_error) {
   die("Ошибка подключения: " . $conn->connect_error);
}  else {
   "Успех";
} ?>
