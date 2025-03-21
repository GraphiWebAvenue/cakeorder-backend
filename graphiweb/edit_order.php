<?php
require '../includes/config.php';  // فایل اتصال به پایگاه داده
session_start();

// بررسی ورود ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت اطلاعات سفارش برای ویرایش
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        header("Location: manage_orders.php");
        exit();
    }
}

// دریافت لیست کاربران، کیک‌ها و شعبات
$stmt = $pdo->query("SELECT id, name FROM users");
$users = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM cakes");
$cakes = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM branches");
$branches = $stmt->fetchAll();

// ویرایش سفارش
if (isset($_POST['edit_order'])) {
    $user_id = $_POST['user_id'];
    $cake_id = $_POST['cake_id'];
    $branch_id = $_POST['branch_id'];
    $total_price = $_POST['total_price'];
    $delivery_method = $_POST['delivery_method'];
    $delivery_date = $_POST['delivery_date'];
    $delivery_time = $_POST['delivery_time'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET user_id = ?, cake_id = ?, branch_id = ?, total_price = ?, delivery_method = ?, delivery_date = ?, delivery_time = ?, status = ? WHERE id = ?");
    $stmt->execute([$user_id, $cake_id, $branch_id, $total_price, $delivery_method, $delivery_date, $delivery_time, $status, $order_id]);

    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Edit Order</h1>

    <!-- فرم ویرایش سفارش -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" class="form-control" required>
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= $user['id'] == $order['user_id'] ? 'selected' : '' ?>><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="cake_id">Cake</label>
            <select name="cake_id" class="form-control" required>
                <option value="">Select Cake</option>
                <?php foreach ($cakes as $cake): ?>
                    <option value="<?= $cake['id'] ?>" <?= $cake['id'] == $order['cake_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cake['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="branch_id">Branch</label>
            <select name="branch_id" class="form-control" required>
                <option value="">Select Branch</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $order['branch_id'] ? 'selected' : '' ?>><?= htmlspecialchars($branch['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="total_price">Total Price</label>
            <input type="number" class="form-control" name="total_price" value="<?= $order['total_price'] ?>" required>
        </div>
        <div class="form-group">
            <label for="delivery_method">Delivery Method</label>
            <input type="text" class="form-control" name="delivery_method" value="<?= $order['delivery_method'] ?>" required>
        </div>
        <div class="form-group">
            <label for="delivery_date">Delivery Date</label>
            <input type="date" class="form-control" name="delivery_date" value="<?= $order['delivery_date'] ?>" required>
        </div>
        <div class="form-group">
            <label for="delivery_time">Delivery Time</label>
            <input type="time" class="form-control" name="delivery_time" value="<?= $order['delivery_time'] ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" class="form-control" name="status" value="<?= $order['status'] ?>" required>
        </div>
        <button type="submit" name="edit_order" class="btn btn-success mt-3">Update Order</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>
