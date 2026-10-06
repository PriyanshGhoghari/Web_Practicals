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

        $folder = dirname(__DIR__, 2) . '/Practical7_storage';

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        $file = fopen($folder . '/registrations.csv', 'a');

        if ($file) {

            $isSuccess = fputcsv($file, [
                $name,
                $email,
                $phone,
                password_hash($password, PASSWORD_DEFAULT)
            ]) !== false;

            fclose($file);
        }
    }
}

echo json_encode(['success' => $isSuccess]);