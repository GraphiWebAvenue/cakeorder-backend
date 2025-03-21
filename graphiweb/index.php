<?php
require '../includes/config.php';
session_start();


// بررسی ورود و سطح دسترسی ادمین
if (isset($_POST['admin_login'])) {
    $email = $_POST['admin_email'];
    $password = $_POST['admin_password'];
    
    $stmt = $pdo->prepare("SELECT id, name, password, role_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // بررسی دسترسی نقش به پنل ادمین
        $role_stmt = $pdo->prepare("SELECT admin_access FROM roles WHERE id = ?");
        $role_stmt->execute([$user['role_id']]);
        $admin_access = $role_stmt->fetchColumn();
        
        if ($admin_access) { // اگر نقش دسترسی به پنل ادمین داشته باشد
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role_id'] = $user['role_id'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Access denied. You do not have admin privileges.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}

if (!isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
}

// دریافت دسترسی‌های نقش کاربر
if (isset($_SESSION['role_id'])) {
    $stmt = $pdo->prepare("SELECT section FROM permissions WHERE role_id = ?");
    $stmt->execute([$_SESSION['role_id']]);
    $user_permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    $user_permissions = [];
}

// تابع برای بررسی دسترسی
function hasAccess($section) {
    global $user_permissions;
    return in_array($section, $user_permissions);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        .dropdown-container {
            position: relative;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            list-style: none;
            padding: 0;
            width: 100%;
            z-index: 1000;
        }
        .dropdown-container:hover .dropdown-menu,
        .dropdown-container .dropdown-menu:hover {
            display: block;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
        }
        .dropdown-menu a:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>


    <div class="container mt-4">
        <h1 class="text-center">Admin Dashboard</h1>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <h3 class='text-center mt-4'>Admin Login</h3>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <form method="POST" class="mb-4 text-center">
                <label>Email:</label>
                <input type="email" name="admin_email" required class="form-control w-50 mx-auto">
                <label>Password:</label>
                <input type="password" name="admin_password" required class="form-control w-50 mx-auto">
                <button type="submit" name="admin_login" class="btn btn-primary mt-3">Login</button>
            </form>
        <?php else: ?>
        <p class="text-center">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
        
        <div class="list-group mt-4">
            <?php if (hasAccess('manage_users') || hasAccess('manage_roles')): ?>
                <div class="dropdown-container">
                    <a href="#" class="list-group-item list-group-item-action">Manage Users ▼</a>
                    <ul class="dropdown-menu">
                        <?php if (hasAccess('manage_users')): ?>
                            <li><a href="manage_users.php">Users</a></li>
                        <?php endif; ?>
                        <?php if (hasAccess('manage_roles')): ?>
                            <li><a href="manage_roles.php">Roles</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (hasAccess('manage_orders')): ?>
                <a href="manage_orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
            <?php endif; ?>
            <?php if (hasAccess('manage_cakes')): ?>
                <a href="manage_cakes.php" class="list-group-item list-group-item-action">Manage Cakes</a>
            <?php endif; ?>
            <?php if (hasAccess('manage_branches')): ?>
                <a href="manage_branches.php" class="list-group-item list-group-item-action">Manage Branches</a>
            <?php endif; ?>
            <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
