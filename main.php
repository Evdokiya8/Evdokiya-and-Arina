<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин мебели</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Подключение вашего файла стилей -->

    <style>
        /* Стили для карточек товаров */
        .card {
            border: 1px solid #ddd; /* Цвет границы карточки */
            border-radius: 8px; /* Закругленные углы карточки */
            transition: transform 0.3s; /* Плавный переход при наведении */
        }

        .card:hover {
            transform: scale(1.05); /* Увеличение карточки при наведении */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Тень при наведении */
        }

        .card-img-top {
            height: 200px; /* Фиксированная высота для изображений товаров */
            object-fit: cover; /* Обеспечение правильного отображения изображения */
        }

        .btn-success {
            background-color: #28a745; /* Цвет кнопки "Купить" */
            border-color: #28a745; /* Цвет границы кнопки "Купить" */
        }

        .btn-success:hover {
            background-color: #218838; /* Цвет кнопки "Купить" при наведении */
            border-color: #1e7e34; /* Цвет границы кнопки "Купить" при наведении */
        }

        h2 {
            margin-bottom: 20px; /* Отступ снизу для заголовка секции товаров */
        }
    </style>
</head>
<body>
    <header class="bg-light p-3">
        <h1>Магазин мебели</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="profile.php">Личный кабинет</a></li>
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
                    echo "<div class='col-lg-2 col-md-3 col-sm-4 mb-4'> 
                            <div class='card'>
                                <img src='".htmlspecialchars($row['image_link'])."' alt='".htmlspecialchars($row['name'])."' class='card-img-top'> 
                                <div class='card-body'>
                                    <h5 class='card-title'>".htmlspecialchars($row['name'])."</h5> 
                                    <p class='card-text'>Цена: ".htmlspecialchars($row['price'])." руб.</p> 
                                    <!-- Форма для оформления заказа -->
                                    <form method='post' action='checkout.php'>
                                        <input type='hidden' name='product_id' value='".htmlspecialchars($row['id'])."'>
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
