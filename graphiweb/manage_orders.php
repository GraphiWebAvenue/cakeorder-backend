<?php
require '../includes/config.php';  // فایل اتصال به پایگاه داده
session_start();

// بررسی ورود ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت لیست سفارش‌ها
$stmt = $pdo->query("SELECT * FROM orders");
$orders = $stmt->fetchAll();

// حذف سفارش
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Manage Orders</h1>

    <!-- دکمه افزودن سفارش جدید -->
    <a href="index.php" class="btn btn-secondary mb-4">Back</a>
    <a href="add_order.php" class="btn btn-primary mb-4">Add New Order</a>

    <!-- جدول سفارش‌ها -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order Date</th>
                <th>User</th>
                <th>Cake</th>
                <th>Branch</th>
                <th>Total Price</th>
                <th>Delivery Method</th>
                <th>Delivery Date</th>
                <th>Delivery Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <?php
                    // دریافت اطلاعات کاربر، کیک و شعبه
                    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                    $stmt->execute([$order['user_id']]);
                    $user = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT name FROM cakes WHERE id = ?");
                    $stmt->execute([$order['cake_id']]);
                    $cake = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT name FROM branches WHERE id = ?");
                    $stmt->execute([$order['branch_id']]);
                    $branch = $stmt->fetchColumn();
                ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['order_date'] ?></td>
                    <td><?= htmlspecialchars($user) ?></td>
                    <td><?= htmlspecialchars($cake) ?></td>
                    <td><?= htmlspecialchars($branch) ?></td>
                    <td><?= $order['total_price'] ?></td>
                    <td><?= htmlspecialchars($order['delivery_method']) ?></td>
                    <td><?= $order['delivery_date'] ?></td>
                    <td><?= $order['delivery_time'] ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>
                        <!-- ویرایش و حذف سفارش -->
                        <a href="edit_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" name="delete_order" class="btn btn-sm btn-danger">Delete</button>
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
