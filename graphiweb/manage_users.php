<?php
require '../includes/config.php';
session_start();

// بررسی ورود ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}
// دریافت دسترسی‌های نقش کاربر
$stmt = $pdo->prepare("SELECT section FROM permissions WHERE role_id = ?");
$stmt->execute([$_SESSION['role_id']]);
$user_permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

// بررسی دسترسی به این صفحه
$current_page = basename($_SERVER['PHP_SELF'], ".php"); // نام صفحه بدون .php

$permission_map = [
    'manage_users' => 'manage_users',
    'manage_roles' => 'manage_roles',
    'manage_orders' => 'manage_orders',
    'manage_cakes' => 'manage_cakes',
    'manage_branches' => 'manage_branches',
];

// اگر دسترسی نداشت، به صفحه اصلی هدایت شود
if (!in_array($permission_map[$current_page] ?? '', $user_permissions)) {
    header("Location: index.php?error=no_access");
    exit();
}

// دریافت لیست کاربران
$stmt = $pdo->query("SELECT id, name, email, role_id FROM users");
$users = $stmt->fetchAll();

// دریافت نقش‌ها
$stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $stmt->fetchAll();

// حذف کاربر
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Manage Users</h1>
                                        <a href="index.php" class="btn btn-secondary mb-4">Back</a>
    <a href="add_user.phpp" class="btn btn-primary mb-4">Add New User</a>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['email'] ?></td>
                         <td><?php 
                            foreach ($roles as $role) {
                                if ($role['id'] == $user['role_id']) {
                                    echo htmlspecialchars($role['name']);
                                    break;
                                }
                            }
                        ?></td>
                        <td>
                  
    <a href='edit_user.php?id=<?= $user["id"] ?>' class='btn btn-sm btn-warning'>Edit</a>
    <form method='POST' style='display:inline;'>
        <input type='hidden' name='user_id' value='<?= $user["id"] ?>'>
        <button type='submit' name='delete_user' class='btn btn-sm btn-danger'>Delete</button>
    </form>
</td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
