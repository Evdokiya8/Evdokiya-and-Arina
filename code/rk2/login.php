<h1 class="mb-5">Login</h1>

<?php flash() ?>

<form method="post" action="do_login.php">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Login</button>
        <a class="btn btn-outline-primary" href="index.php">Register</a>
    </div>
</form>