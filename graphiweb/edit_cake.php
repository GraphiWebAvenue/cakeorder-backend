<?php
require '../includes/config.php';
session_start();

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت اطلاعات کیک
if (!isset($_GET['id'])) {
    header("Location: manage_cakes.php");
    exit();
}

$cake_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cakes WHERE id = ?");
$stmt->execute([$cake_id]);
$cake = $stmt->fetch();

if (!$cake) {
    header("Location: manage_cakes.php");
    exit();
}

// دریافت لیست شعبه‌ها برای نمایش در چک‌باکس‌ها
$stmt = $pdo->query("SELECT id, name FROM branches");
$branches = $stmt->fetchAll();

// دریافت شعبه‌های مرتبط با این کیک
$stmt = $pdo->prepare("SELECT branch_id FROM cake_branch WHERE cake_id = ?");
$stmt->execute([$cake_id]);
$assigned_branches = $stmt->fetchAll(PDO::FETCH_COLUMN);

// بروزرسانی اطلاعات کیک
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $base_price = $_POST['base_price'];
    $pricing_type = $_POST['pricing_type'];
    $price_per_slice = ($pricing_type === 'multi') ? $_POST['price_per_slice'] : NULL;
    $price_half = ($pricing_type === 'multi') ? $_POST['price_half'] : NULL;
    
    // بروزرسانی کیک در جدول cakes
    $stmt = $pdo->prepare("UPDATE cakes SET name = ?, description = ?, base_price = ?, pricing_type = ?, price_per_slice = ?, price_half = ? WHERE id = ?");
    $stmt->execute([$name, $description, $base_price, $pricing_type, $price_per_slice, $price_half, $cake_id]);

    // حذف ارتباط‌های قبلی از جدول cake_branch
    $stmt = $pdo->prepare("DELETE FROM cake_branch WHERE cake_id = ?");
    $stmt->execute([$cake_id]);

    // اضافه کردن ارتباط‌های جدید
    if (isset($_POST['branches'])) {
        foreach ($_POST['branches'] as $branch_id) {
            $stmt = $pdo->prepare("INSERT INTO cake_branch (cake_id, branch_id) VALUES (?, ?)");
            $stmt->execute([$cake_id, $branch_id]);
        }
    }

    header("Location: manage_cakes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Edit Cake</h1>
        <form method="POST" class="w-50 mx-auto">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($cake['name']) ?>" required class="form-control">
            <label>Description:</label>
            <textarea name="description" required class="form-control"><?= htmlspecialchars($cake['description']) ?></textarea>
            <label>Base Price (€):</label>
            <input type="number" name="base_price" step="0.01" value="<?= number_format($cake['base_price'], 2) ?>" required class="form-control">
            <label>Pricing Type:</label>
            <select name="pricing_type" class="form-control" id="pricing_type">
                <option value="single" <?= ($cake['pricing_type'] === 'single') ? 'selected' : '' ?>>Single-Priced</option>
                <option value="multi" <?= ($cake['pricing_type'] === 'multi') ? 'selected' : '' ?>>Multi-Priced</option>
            </select>
            <div id="multi_price_fields" style="display: <?= ($cake['pricing_type'] === 'multi') ? 'block' : 'none' ?>;">
                <label>Price Per Slice (€):</label>
                <input type="number" name="price_per_slice" step="0.01" value="<?= number_format($cake['price_per_slice'], 2) ?>" class="form-control">
                <label>Price Half Cake (€):</label>
                <input type="number" name="price_half" step="0.01" value="<?= number_format($cake['price_half'], 2) ?>" class="form-control">
            </div>

            <h4>Select Branches</h4>
            <div>
                <?php foreach ($branches as $branch): ?>
                    <div class="form-check">
                        <input type="checkbox" name="branches[]" value="<?= $branch['id'] ?>" class="form-check-input" 
                            <?= in_array($branch['id'], $assigned_branches) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= htmlspecialchars($branch['name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-warning mt-3">Update Cake</button>
        </form>
        <a href="manage_cakes.php" class="btn btn-secondary mt-3">Back to Cakes</a>
    </div>
    <script>
        document.getElementById('pricing_type').addEventListener('change', function () {
            document.getElementById('multi_price_fields').style.display = this.value === 'multi' ? 'block' : 'none';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
