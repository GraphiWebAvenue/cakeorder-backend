<?php
require '../includes/config.php';
session_start();

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت اطلاعات کاربر
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

// دریافت نقش‌ها
$stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $stmt->fetchAll();

// بروزرسانی اطلاعات کاربر
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role_id = ? WHERE id = ?");
        $stmt->execute([$name, $email, $password, $role_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role_id = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role_id, $user_id]);
    }
    
    header("Location: manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Edit User</h1>
        <form method="POST" class="w-50 mx-auto">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="form-control">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="form-control">
            <label>New Password (leave blank to keep current password):</label>
            <input type="password" name="password" class="form-control">
            <label>Role:</label>
            <select name="role_id" class="form-control w-100" >
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= ($role['id'] == $user['role_id']) ? 'selected' : '' ?>><?= $role['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-warning mt-3">Update User</button>
        </form>
        <a href="manage_users.php" class="btn btn-secondary mt-3">Back to Users</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
