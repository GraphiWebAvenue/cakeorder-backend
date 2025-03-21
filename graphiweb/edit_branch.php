<?php
require '../includes/config.php';  // فایل اتصال به پایگاه داده
session_start();

// بررسی ورود ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت اطلاعات شعبه برای ویرایش
if (isset($_GET['id'])) {
    $branch_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
    $stmt->execute([$branch_id]);
    $branch = $stmt->fetch();

    if (!$branch) {
        // اگر شعبه پیدا نشد، هدایت به صفحه مدیریت شعبه‌ها
        header("Location: manage_branches.php");
        exit();
    }
}

// به‌روزرسانی اطلاعات شعبه
if (isset($_POST['update_branch'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
    $email = $_POST['email'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $pdo->prepare("UPDATE branches SET name = ?, phone = ?, city = ?, address = ?, postal_code = ?, email = ?, latitude = ?, longitude = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $city, $address, $postal_code, $email, $latitude, $longitude, $branch_id]);

    header("Location: manage_branches.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Branch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Edit Branch</h1>

    <!-- فرم ویرایش شعبه -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Branch Name</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($branch['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($branch['phone']) ?>" required>
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($branch['city']) ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($branch['address']) ?>" required>
        </div>
        <div class="form-group">
            <label for="postal_code">Postal Code</label>
            <input type="text" class="form-control" name="postal_code" value="<?= htmlspecialchars($branch['postal_code']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($branch['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="text" class="form-control" name="latitude" value="<?= htmlspecialchars($branch['latitude']) ?>" required>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="text" class="form-control" name="longitude" value="<?= htmlspecialchars($branch['longitude']) ?>" required>
        </div>
        <button type="submit" name="update_branch" class="btn btn-success mt-3">Update Branch</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>
