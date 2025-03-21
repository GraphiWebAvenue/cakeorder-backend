<?php
require 'includes/config.php';

// بررسی ارسال کد پستی
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postal_code = trim($_POST['postal_code']);  // حذف فاصله‌های اضافی

    echo "<h4>Showing cakes available near: $postal_code</h4>";

    // دریافت مختصات کد پستی از جدول postal_codes
    $stmt = $pdo->prepare("SELECT latitude, longitude FROM postal_codes WHERE postal_code = ? LIMIT 1");
    $stmt->execute([$postal_code]);
    $user_location = $stmt->fetch();

    if ($user_location) {
        // یافتن نزدیک‌ترین شعبه با دقت بیشتر در فاصله‌سنجی Haversine
        $stmt = $pdo->prepare("SELECT id, name, address, city, latitude, longitude, 
            ROUND((6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))), 2) AS distance 
            FROM branches 
            ORDER BY distance ASC 
            LIMIT 1");
        $stmt->execute([$user_location['latitude'], $user_location['longitude'], $user_location['latitude']]);
        $branch = $stmt->fetch();

        if ($branch) {
            echo "<p>Closest Branch: <strong>{$branch['name']}</strong> - {$branch['address']} ({$branch['city']})</p>";

            // دریافت کیک‌های موجود در این شعبه
            $stmt = $pdo->prepare("SELECT cakes.* FROM cakes 
                                  JOIN cake_branch ON cakes.id = cake_branch.cake_id 
                                  WHERE cake_branch.branch_id = ?");
            $stmt->execute([$branch['id']]);
            $cakes = $stmt->fetchAll();

            if ($cakes) {
                echo "<div class='row'>";
                foreach ($cakes as $cake) {
                    echo "<div class='col-md-4'>
                            <div class='card'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$cake['name']}</h5>
                                    <p class='card-text'>{$cake['description']}</p>
                                    <p class='card-text'><strong>Price: €{$cake['base_price']}</strong></p>
                                    <a href='order.php?cake_id={$cake['id']}' class='btn btn-success'>Order Now</a>
                                </div>
                            </div>
                          </div>";
                }
                echo "</div>";
            } else {
                echo "<p class='text-danger'>No cakes found in this branch.</p>";
            }
        } else {
            echo "<p class='text-danger'>No branches found near this postal code.</p>";
        }
    } else {
        echo "<p class='text-danger'>Invalid postal code. Please try again.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Order Shop</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Welcome to Cake Order Shop</h1>
        
        <!-- بخش دریافت کد پستی -->
        <form action="index.php" method="POST" class="mb-4">
            <label for="postal_code">Enter Your Dutch Postal Code:</label>
            <input type="text" name="postal_code" required class="form-control w-50">
            <button type="submit" class="btn btn-primary mt-2">Find Cakes</button>
        </form>
    </div>
    <script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
