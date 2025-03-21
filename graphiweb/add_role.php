<?php
require '../includes/config.php';
session_start();

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت لیست داینامیک بخش‌ها از دیتابیس
$sections_stmt = $pdo->query("SELECT section_name FROM sections");
$sections = $sections_stmt->fetchAll(PDO::FETCH_COLUMN);

// اضافه کردن نقش جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $admin_access = isset($_POST['admin_access']) ? 1 : 0;
    $selected_permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    
    $stmt = $pdo->prepare("INSERT INTO roles (name, admin_access) VALUES (?, ?)");
    $stmt->execute([$name, $admin_access]);
    $role_id = $pdo->lastInsertId();
    
    foreach ($selected_permissions as $section) {
        $stmt = $pdo->prepare("INSERT INTO permissions (role_id, section) VALUES (?, ?)");
        $stmt->execute([$role_id, $section]);
    }
    
    header("Location: manage_roles.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Add New Role</h1>
        <form method="POST" class="w-50 mx-auto">
            <label>Role Name:</label>
            <input type="text" name="name" required class="form-control">
            
            <label class="mt-3">Admin Access:</label>
            <div class="form-check">
                <input type="checkbox" name="admin_access" value="1" class="form-check-input">
                <label class="form-check-label"> Allow Access to Admin Panel</label>
            </div>
            
            <label class="mt-3">Permissions:</label>
            <div class="form-check">
                <?php foreach ($sections as $section): ?>
                    <input type="checkbox" name="permissions[]" value="<?= $section ?>" class="form-check-input">
                    <label class="form-check-label"> <?= ucfirst(str_replace('_', ' ', $section)) ?> </label><br>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn btn-success mt-3">Add Role</button>
        </form>
        <a href="manage_roles.php" class="btn btn-secondary mt-3">Back to Roles</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
