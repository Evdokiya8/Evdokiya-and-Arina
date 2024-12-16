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
    <header class="bg-light p-3">
        <h1>Магазин мебели</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="profile.php">Личный кабинет</a></li>
                <li class="nav-item"><a class="nav-link" href="view_orders.php">Мои заявки</a></li>
                <li class="nav-item"><a class="nav-link" href="#products">Продукты</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">О Нас</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
            </ul>
        </nav>
    </header>

    <section id="products" class="container mt-5">
        <h2>Наши товары</h2>
        <div class="row">
            <?php  
            require('database.php');  

            $SQL = "SELECT id, description, name, image_link, price FROM products 
                    UNION ALL 
                    SELECT id, description, name, image_link, price FROM goods"; 
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
                                    <!-- Форма для оформления заказа -->
                                    <form method='post' action='checkout.php'>
                                        <input type='hidden' name='product_id' value='{$row['id']}'>
                                        <button type='submit' class='btn btn-success'>Купить</button>  
                                    </form>
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
    </section>

    <footer> 
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p> 
    </footer>

   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script> 


</body>
</html>
