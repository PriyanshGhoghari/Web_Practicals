<?php
session_start();


if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/db.php';


$stmt = $conn->prepare('SELECT id, name, role FROM users WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: logout.php');
    exit;
}


function h($text) {
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}
