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
                <li><a href="my_project/register.html">Личный кабинет</a></li>
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
        <a href="#products" class="btn">Смотреть предложения</a>
    </section>
    <section id="products">
        <h2>Наши товары</h2>
        <div class="products-container">
            <?php 
		error_reporting(E_ALL); // отображение ошибок для отладки
		ini_set('display_errors', 1);
	require('database.php');
	$SQL = "SELECT `id`, `description`, `name`, `image_link`, `price` FROM `goods`";
	$result = mysqli_query($conn, $SQL); // Используем $conn вместо $db
		if (!$result) {
    	die("Couldn't execute query: " . mysqli_error($conn));
	}
	if (mysqli_num_rows($result) > 0) {
    	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo "<div class='product'>
                <img src='{$row['image_link']}' alt='{$row['name']}'>
                <h3>{$row['name']}</h3>
                <p>Цена: {$row['price']} руб.</p>
                <button>Купить</button>
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
