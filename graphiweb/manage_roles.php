<?php
require '../includes/config.php';
session_start();

// بررسی ورود ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// دریافت لیست نقش‌ها
$stmt = $pdo->query("SELECT * FROM roles");
$roles = $stmt->fetchAll();

// حذف نقش
if (isset($_POST['delete_role'])) {
    $role_id = $_POST['role_id'];
    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
    $stmt->execute([$role_id]);
    header("Location: manage_roles.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Manage Roles</h1>
                                <a href="index.php" class="btn btn-secondary mb-4">Back</a>
    <a href="add_role.php" class="btn btn-primary mb-4">Add New Role</a>
        <table class="table table-bordered mt-4">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Admin Access</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT section FROM permissions WHERE role_id = ?");
                    $stmt->execute([$role['id']]);
                    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $permissions_list = !empty($permissions) ? implode(', ', array_map(function($p) { return ucfirst(str_replace('_', ' ', $p)); }, $permissions)) : 'No Permissions';
                    ?>
                    <tr>
                        <td><?= $role['id'] ?></td>
                        <td><?= htmlspecialchars($role['name']) ?></td>
                        <td><?= htmlspecialchars($role['name']) ?></td>
                        <td><?= htmlspecialchars($permissions_list) ?></td>
                        <td>
                            <a href='edit_role.php?id=<?= $role["id"] ?>' class='btn btn-sm btn-warning'>Edit</a>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='role_id' value='<?= $role["id"] ?>'>
                                <button type='submit' name='delete_role' class='btn btn-sm btn-danger'>Delete</button>
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
