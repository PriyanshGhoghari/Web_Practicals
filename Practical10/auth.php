<?php
require __DIR__ . '/session.php';
header('Cache-Control: no-store');

if (empty($_SESSION['student_id'])) {
    header('Location: login.html');
    exit;
}

if (sessionTimedOut()) {
    clearSession();
    header('Location: login.html?timeout=1');
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'student' && $role !== 'admin') {
    clearSession();
    header('Location: login.html');
    exit;
}

if (isset($requiredRole) && $role !== $requiredRole) {
    if ($role === 'admin') {
        header('Location: admin-dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$_SESSION['last_activity'] = time();
