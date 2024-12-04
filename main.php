<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин мебели</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Магазин мебели</h1>
        <nav>
            <ul>
                <li><a href="profile.php">Личный кабинет</a></li> 
                <li><a href="#products">Продукты</a></li>
                <li><a href="#about">О Нас</a></li>
                <li><a href="#contact">Контакты</a></li>
            </ul>
        </nav>
    </header>

    <section class="banner">
        <h2>Лучшие предложения недели!</h2>
        <p>Скидки до 50% на избранные товары. Успейте купить!</p>
        <p>Качественная мебель для вашего дома</p>
        <a href="#products" class="btn btn-primary">Смотреть предложения</a>  
    </section>

    <section id="products">
        <h2>Наши товары</h2>
        <div class="products-container">
            <?php  
            require('database.php');  

            $SQL = "SELECT id, description, name, image_link, price FROM goods"; 
            $result = mysqli_query($conn, $SQL);  
            if (!$result) { 
                die("Couldn't execute query: " . mysqli_error($conn)); 
            } 

            if (mysqli_num_rows($result) > 0) { 
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { 
                    echo "<div class='product'> 
                            <img src='{$row['image_link']}' alt='{$row['name']}' class='img-fluid'> 
                            <h3>{$row['name']}</h3> 
                            <p>Цена: {$row['price']} руб.</p> 
                            <button class='btn btn-success'>Купить</button>  
                          </div>"; 
                } 
            } else { 
                echo "<p>Товары не найдены.</p>"; 
            } 

            mysqli_close($conn); 
            ?> 
        </div>
    </section>

    <footer> 
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p> 
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script> 

</body>
</html>
