<?php
require __DIR__ . '/session.php';
clearSession();
header('Location: login.html');
exit;
