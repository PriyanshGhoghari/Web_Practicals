<?php

header('Content-Type: application/json');

$isSuccess = false;
$message = 'Please submit the registration form';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']) ?? '';
    $email = trim($_POST['email']) ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $isValid =
        preg_match('/^[A-Za-z\s]{3,}$/', $name) &&
        strlen($name) <= 100 &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        strlen($email) <= 254 &&
        preg_match('/^[6-9][0-9]{9}$/', $phone) &&
        preg_match('/^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/', $password) &&
        $password === $confirmPassword;

    if (!$isValid) {
        $message = 'Invalid registration details';
    } else {
        try {
            require __DIR__ . '/db.php';

            $check = $conn->prepare('SELECT student_id FROM students WHERE email = ?');
            $check->bind_param('s', $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = 'Email already registered';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $statement = $conn->prepare(
                    'INSERT INTO students (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)'
                );
                $statement->bind_param('ssss', $name, $email, $phone, $passwordHash);
                $statement->execute();
                $statement->close();

                $isSuccess = true;
                $message = 'Registration successful';
            }

            $check->close();
            $conn->close();
        } catch (mysqli_sql_exception $error) {
            $message = 'Could not save registration';
        }
    }
}

echo json_encode([
    'success' => $isSuccess,
    'message' => $message
]);
