<?php
header('Content-Type: application/json');

$success = false;
$message = 'Invalid registration details';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $valid = preg_match('/^[A-Za-z\s]{3,}$/', $name) &&
             filter_var($email, FILTER_VALIDATE_EMAIL) &&
             preg_match('/^[6-9][0-9]{9}$/', $phone) &&
             preg_match('/^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/', $password) &&
             $password === $confirmPassword;

    if ($valid) {
        try {
            require __DIR__ . '/db.php';
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO students (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $email, $phone, $hash);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            $success = true;
            $message = 'Registration successful';
        } catch (mysqli_sql_exception $error) {
            $message = $error->getCode() == 1062
                ? 'Email already registered'
                : 'Could not save registration';
        }
    }
}

echo json_encode([
    'success' => $success,
    'message' => $message
]);
