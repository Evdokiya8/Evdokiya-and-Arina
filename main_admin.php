<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор - Магазин мебели</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Администратор - Магазин мебели</h1>
        <nav>
            <ul>
                <li><a href="admin.php">Личный кабинет</a></li>
                <li><a href="manage_orders2.php">Заявки</a></li>
                <li><a href="add_product.php">Добавить товар</a></li>
            </ul>
        </nav>
    </header>

    <section class="banner">
        <h2>Управление продуктами</h2>
        <p>Здесь вы можете управлять всеми товарами в магазине.</p>
    </section>

    <!-- Раздел для продуктов -->
    <section id="products">
        <h2>Все товары</h2>
        <div class="products-container row">
            <?php  
            require('database.php');  

            // Объединение товаров из двух таблиц
            $SQL = "SELECT id, name, description, price, image_link FROM products 
                    UNION ALL 
                    SELECT id, name, description, price, image_link FROM goods"; 
            $result = mysqli_query($conn, $SQL);  
            if (!$result) { 
                die("Couldn't execute query: " . mysqli_error($conn)); 
            } 

            if (mysqli_num_rows($result) > 0) { 
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { 
                    echo "<div class='product col-md-4 mb-4'> 
                            <div class='card'>
                                <img src='".htmlspecialchars($row['image_link'])."' alt='".htmlspecialchars($row['name'])."' class='card-img-top'> 
                                <div class='card-body'>
                                    <h5 class='card-title'>".htmlspecialchars($row['name'])."</h5> 
                                    <p class='card-text'>Цена: ".htmlspecialchars($row['price'])." руб.</p> 
                                    <!-- Кнопка редактирования -->
                                    <form method='get' action='edit_product.php' style='display:inline;'>
                                        <input type='hidden' name='product_id' value='".htmlspecialchars($row['id'])."'>
                                        <button type='submit' class='btn btn-warning'>Редактировать</button>  
                                    </form>
                                    <!-- Кнопка удаления -->
                                    <form method='post' action='delete_product.php' style='display:inline;'>
                                        <input type='hidden' name='product_id' value='".htmlspecialchars($row['id'])."'>
                                        <button type='submit' class='btn btn-danger'>Удалить</button>  
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

    <!-- Подвал -->
    <footer>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
    </footer>

    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script> 

</body>
</html>
