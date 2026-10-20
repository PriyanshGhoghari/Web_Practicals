<?php
require __DIR__ . '/session.php';
header('Content-Type: application/json');

$success = false;
$message = 'Invalid email or password';
$redirect = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {
        try {
            require __DIR__ . '/db.php';
            $stmt = $conn->prepare('SELECT student_id, password_hash, role FROM students WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($studentId, $passwordHash, $role);

            if ($stmt->fetch() && password_verify($password, $passwordHash) &&
                ($role === 'student' || $role === 'admin')) {
                session_regenerate_id(true);
                $_SESSION['student_id'] = $studentId;
                $_SESSION['role'] = $role;
                $_SESSION['last_activity'] = time();

                $success = true;
                $message = 'Login successful';
                $redirect = $role === 'admin' ? 'admin-dashboard.php' : 'dashboard.php';
            }

            $stmt->close();
            $conn->close();
        } catch (mysqli_sql_exception $error) {
            $message = 'Could not log in. Please try again';
        }
    }
}

echo json_encode([
    'success' => $success,
    'message' => $message,
    'redirect' => $redirect
]);
