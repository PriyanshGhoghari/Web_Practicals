<?php
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(strip_tags($_POST['name'] ?? ''));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $isValid =
        preg_match('/^[A-Za-z\s]{3,}$/', $name) &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^[6-9][0-9]{9}$/', $phone) &&
        preg_match('/^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/', $password) &&
        $password === $confirmPassword;

    if ($isValid) {
        try {
            require __DIR__ . '/db.php';

            $query = 'INSERT INTO students (full_name, email, phone, password_hash)
                      VALUES (:full_name, :email, :phone, :password_hash)';
            $statement = $pdo->prepare($query);

            $isSuccess = $statement->execute([
                'full_name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $error) {
            $isSuccess = false;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => $isSuccess]);
