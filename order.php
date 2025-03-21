<?php
require 'includes/config.php';
session_start();

// فعال‌سازی نمایش خطاهای PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['cake_id']) || empty($_GET['cake_id'])) {
    die("Invalid cake selection.");
}

$cake_id = $_GET['cake_id'];

// دریافت اطلاعات کیک
$stmt = $pdo->prepare("SELECT * FROM cakes WHERE id = ?");
$stmt->execute([$cake_id]);
$cake = $stmt->fetch();

if (!$cake) {
    die("Cake not found.");
}

// دریافت نزدیک‌ترین شعبه بر اساس کیک انتخاب شده
$stmt = $pdo->prepare("SELECT branches.id, branches.name, branches.address, branches.city 
                      FROM branches 
                      JOIN cake_branch ON branches.id = cake_branch.branch_id 
                      WHERE cake_branch.cake_id = ?
                      ORDER BY branches.id ASC LIMIT 1");
$stmt->execute([$cake_id]);
$branch = $stmt->fetch();

if (!$branch) {
    die("No available branches for this cake.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order']) && isset($_SESSION['user_id'])) {
    $delivery_date = $_POST['delivery_date'];
    $delivery_time = $_POST['delivery_time'];
    $delivery_method = $_POST['delivery_method'];
    $total_price = $cake['base_price'];
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, cake_id, branch_id, delivery_date, delivery_time, delivery_method, total_price, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $cake_id, $branch['id'], $delivery_date, $delivery_time, $delivery_method, $total_price]);
        
        $order_success_message = "<p class='text-success mt-3'>Order placed successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];
    
    $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        
    } else {
        echo "<p class='text-danger text-center'>Invalid email or password.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cake</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Order Cake</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class='text-success text-center'>Welcome, <?= $_SESSION['user_name'] ?>! <a href='logout.php' class='btn btn-danger btn-sm'>Logout</a></p>
            <?php if (isset($order_success_message)) echo $order_success_message; ?>
            <form method="POST">
                <label for="delivery_date">Delivery Date:</label>
                <input type="date" name="delivery_date" required class="form-control w-50">
                
                <label for="delivery_time">Delivery Time:</label>
                <input type="time" name="delivery_time" required class="form-control w-50">
                
                <label for="delivery_method">Delivery Method:</label>
                <select name="delivery_method" class="form-control w-50">
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                </select>
                
                <button type="submit" name="order" class="btn btn-success mt-3">Place Order</button>
            </form>
        <?php else: ?>
            <h3 class='text-center mt-4'>Register or Login to place an order</h3>
            <form method="POST" class="mb-4">
                <input type="hidden" name="register" value="1">
                <label>Name:</label>
                <input type="text" name="name" required class="form-control">
                <label>Email:</label>
                <input type="email" name="email" required class="form-control">
                <label>Password:</label>
                <input type="password" name="password" required class="form-control">
                <button type="submit" class="btn btn-primary mt-3">Register</button>
            </form>
            <p class='text-center'>or</p>
            <form method="POST" class="mb-4">
                <input type="hidden" name="login" value="1">
                <label>Email:</label>
                <input type="email" name="login_email" required class="form-control">
                <label>Password:</label>
                <input type="password" name="login_password" required class="form-control">
                <button type="submit" class="btn btn-secondary mt-3">Login</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
