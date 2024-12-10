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
                <li><a href="#orders">Заявки</a></li>
                <li><a href="#products">Продукты</a></li>
                <li><a href="add_product.php">Добавить товар</a></li>
                <li><a href="#contact">Контакты</a></li>
            </ul>
        </nav>
    </header>

    <section class="banner">
        <h2>Управление заявками и продуктами</h2>
        <p>Здесь вы можете управлять всеми заявками и товарами в магазине.</p>
    </section>

    <section id="orders">
        <h2>Заявки</h2>

        <div class="filter mb-3">
            <label for="statusFilter">Фильтр по статусу:</label>
            <select id="statusFilter" class="form-control" onchange="filterOrders()">
                <option value="">Все статусы</option>
                <option value="pending">В ожидании</option>
                <option value="completed">Завершено</option>
                <option value="canceled">Отменено</option>
            </select>
        </div>

        <div class="search-filter mb-3">
            <input type="text" id="search" placeholder="Поиск по заявкам..." class="form-control">
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата заказа</th>
                    <th>Статус заказа</th>
                    <th>ID клиента</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="orders-table-body">
                <?php
                require('database.php');

                $SQL = "SELECT id, order_date, order_status, id_clients FROM orders";
                $result = mysqli_query($conn, $SQL);
                
                if (!$result) {
                    die("Couldn't execute query: " . mysqli_error($conn));
                }

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        echo "<tr data-id='{$row['id']}' data-status='{$row['order_status']}'>
                                <td>{$row['id']}</td>
                                <td>{$row['order_date']}</td>
                                <td>{$row['order_status']}</td>
                                <td>{$row['id_clients']}</td>
                                <td>
                                    <!-- Форма для изменения статуса -->
                                    <form method='post' action='update_order_status.php' style='display:inline;'>
                                        <input type='hidden' name='order_id' value='{$row['id']}'>
                    }
                } else {
                    echo "<tr><td colspan='5'>Заявки не найдены.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </section>
    <section id="products">
        <h2>Продукты</h2>
    </section>

    <footer>
        <p>&copy; 2024 Магазин мебели. Все права защищены.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#orders-table-body tr');
            rows.forEach(row => {
                const clientId = row.cells[3].textContent.toLowerCase();
                if (clientId.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function filterOrders() {
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#orders-table-body tr');
            rows.forEach(row => {
                const orderStatus = row.dataset.status;
                if (statusFilter === '' || orderStatus === statusFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html
