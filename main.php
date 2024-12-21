<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин мебели</title>
    <link rel="stylesheet" href="styles.css">
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

    <section id="products">
        <h2>Наши товары</h2>
        <div style="display: flex; flex-wrap: wrap; justify-content: center;">
            <?php  
            require('database.php');  

            // Запрос на получение товаров
            $SQL = "SELECT id, description, name, image_link, price FROM products 
                    UNION ALL 
                    SELECT id, description, name, image_link, price FROM goods"; 
            $result = mysqli_query($conn, $SQL);  
            if (!$result) { 
                die("Не удалось выполнить запрос: " . mysqli_error($conn)); 
            } 

            if (mysqli_num_rows($result) > 0) { 
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { 
                    echo "<div style='width: 18%; margin: 10px; border: 1px solid #ddd; padding: 10px; text-align: center;'> 
                            <img src='".htmlspecialchars($row['image_link'])."' alt='".htmlspecialchars($row['name'])."' style='max-width: 100%; height: auto;'> 
                            <h5>".htmlspecialchars($row['name'])."</h5> 
                            <p>Цена: ".htmlspecialchars($row['price'])." руб.</p> 
                            <!-- Форма для оформления заказа -->
                            <form method='post' action='checkout.php'>
                                <input type='hidden' name='product_id' value='".htmlspecialchars($row['id'])."'>
                                <button type='submit'>Купить</button>  
                            </form>
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

</body>
</html>
