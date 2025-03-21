<?php
require '../includes/config.php';  // فایل اتصال به پایگاه داده
session_start();

// بررسی ورود ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت لیست شعبه‌ها
$stmt = $pdo->query("SELECT * FROM branches");
$branches = $stmt->fetchAll();

// حذف شعبه
if (isset($_POST['delete_branch'])) {
    $branch_id = $_POST['branch_id'];
    $stmt = $pdo->prepare("DELETE FROM branches WHERE id = ?");
    $stmt->execute([$branch_id]);
    header("Location: manage_branches.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Branches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Manage Branches</h1>

    <!-- دکمه افزودن شعبه جدید -->
    <a href="index.php" class="btn btn-secondary mb-4">Back</a>
    <a href="add_branch.php" class="btn btn-primary mb-4">Add New Branch</a>
    

    <!-- جدول شعبه‌ها -->
    <h4>Existing Branches</h4>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Branch Name</th>
                <th>Phone</th>
                <th>City</th>
                <th>Address</th>
                <th>Postal Code</th>
                <th>Email</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($branches as $branch): ?>
                <tr>
                    <td><?= $branch['id'] ?></td>
                    <td><?= htmlspecialchars($branch['name']) ?></td>
                    <td><?= htmlspecialchars($branch['phone']) ?></td>
                    <td><?= htmlspecialchars($branch['city']) ?></td>
                    <td><?= htmlspecialchars($branch['address']) ?></td>
                    <td><?= htmlspecialchars($branch['postal_code']) ?></td>
                    <td><?= htmlspecialchars($branch['email']) ?></td>
                    <td><?= htmlspecialchars($branch['latitude']) ?></td>
                    <td><?= htmlspecialchars($branch['longitude']) ?></td>
                    <td>
                        <!-- ویرایش و حذف شعبه -->
                        <a href="edit_branch.php?id=<?= $branch['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="branch_id" value="<?= $branch['id'] ?>">
                            <button type="submit" name="delete_branch" class="btn btn-sm btn-danger">Delete</button>
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
