<?php
require '../includes/config.php';
header('Content-Type: application/json');

// بررسی متد درخواست
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // بررسی اعتبار توکن JWT
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        echo json_encode(['message' => 'Authorization token is missing']);
        exit();
    }

    $authorization_header = $_SERVER['HTTP_AUTHORIZATION'];
    $token = str_replace('Bearer ', '', $authorization_header);

    // اعتبارسنجی توکن JWT
    $secret_key = 'your_secret_key';
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
    } catch (Exception $e) {
        echo json_encode(['message' => 'Invalid or expired token']);
        exit();
    }

    // دریافت کد پستی از بدن درخواست
    $data = json_decode(file_get_contents("php://input"));
    $postal_code = htmlspecialchars($data->postal_code, ENT_QUOTES, 'UTF-8'); // جلوگیری از XSS

    // جستجو در پایگاه داده برای دریافت شعبه‌ها
    $stmt = $pdo->prepare("SELECT id, name, address, city, latitude, longitude FROM branches WHERE postal_code = ?");
    $stmt->execute([$postal_code]);
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($branches) {
        echo json_encode(['branches' => $branches]);
    } else {
        echo json_encode(['message' => 'No branches found for this postal code.']);
    }
}
?>
