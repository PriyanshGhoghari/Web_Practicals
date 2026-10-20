<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/db.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    
    $stmt = $conn->prepare('SELECT id, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        
        if ($user['role'] === 'admin') {
            $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, 'Admin logged in')");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
        }

        header('Location: index.php');
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Practical 14 - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h1>Practical 14 - Admin Dashboard</h1>
    <section>
        <h2>Login</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <form method="post">
            <label>Email <input type="email" name="email" required></label>
            <label>Password <input type="password" name="password" required></label>
            <button>Login</button>
        </form>
        <p><strong>Admin:</strong> admin@example.com / demo123</p>
        <p><strong>Student:</strong> student@example.com / demo123</p>
    </section>
</main>
</body>
</html>
