<?php
require '../includes/config.php';
session_start();

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت نقش‌ها
$stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $stmt->fetchAll();

// افزودن کاربر جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role_id]);
    
    header("Location: manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Add New User</h1>
        <form method="POST" class="w-50 mx-auto">
            <label>Name:</label>
            <input type="text" name="name" required class="form-control">
            <label>Email:</label>
            <input type="email" name="email" required class="form-control">
            <label>Password:</label>
            <input type="password" name="password" required class="form-control">
            <label>Role:</label>
            <select name="role_id" class="form-control w-100">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-success mt-3">Add User</button>
        </form>
        <a href="manage_users.php" class="btn btn-secondary mt-3">Back to Users</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
