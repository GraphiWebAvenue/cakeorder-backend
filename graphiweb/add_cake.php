<?php
require '../includes/config.php';
session_start();

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت لیست شعبه‌ها
$stmt = $pdo->query("SELECT id, name FROM branches");
$branches = $stmt->fetchAll();

// اضافه کردن کیک جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $base_price = $_POST['base_price'];
    $pricing_type = $_POST['pricing_type'];
    $price_per_slice = $pricing_type === 'multi' ? $_POST['price_per_slice'] : NULL;
    $price_half = $pricing_type === 'multi' ? $_POST['price_half'] : NULL;

    // درج کیک جدید
    $stmt = $pdo->prepare("INSERT INTO cakes (name, description, base_price, pricing_type, price_per_slice, price_half) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $base_price, $pricing_type, $price_per_slice, $price_half]);

    // دریافت id آخرین کیک وارد شده
    $cake_id = $pdo->lastInsertId();

    // اضافه کردن شعبه‌ها به جدول cake_branch
    if (isset($_POST['branches']) && is_array($_POST['branches'])) {
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
    <title>Add Cake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Add New Cake</h1>
        <form method="POST" class="w-50 mx-auto">
            <label>Name:</label>
            <input type="text" name="name" required class="form-control">
            <label>Description:</label>
            <textarea name="description" required class="form-control"></textarea>
            <label>Base Price (€):</label>
            <input type="number" name="base_price" step="0.01" required class="form-control">
            <label>Pricing Type:</label>
            <select name="pricing_type" class="form-control" id="pricing_type">
                <option value="single">Single-Priced</option>
                <option value="multi">Multi-Priced</option>
            </select>
            <div id="multi_price_fields" style="display: none;">
                <label>Price Per Slice (€):</label>
                <input type="number" name="price_per_slice" step="0.01" class="form-control">
                <label>Price Half Cake (€):</label>
                <input type="number" name="price_half" step="0.01" class="form-control">
            </div>

            <label>Choose Branches:</label><br>
            <?php foreach ($branches as $branch): ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="branches[]" value="<?= $branch['id'] ?>" id="branch_<?= $branch['id'] ?>">
                    <label class="form-check-label" for="branch_<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></label>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-success mt-3">Add Cake</button>
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
