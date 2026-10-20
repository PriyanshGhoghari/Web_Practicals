<?php
session_start();

function sessionTimedOut() {
    return isset($_SESSION['last_activity']) &&
           time() - $_SESSION['last_activity'] >= 15 * 60;
}

function clearSession() {
    $_SESSION = [];
    $path = session_get_cookie_params()['path'];
    setcookie(session_name(), '', time() - 3600, $path);
    session_destroy();
}
