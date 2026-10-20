<?php
session_start();
$_SESSION = [];
$path = session_get_cookie_params()['path'];
setcookie(session_name(), '', time() - 3600, $path);
session_destroy();
header('Location: login.php');
exit;
