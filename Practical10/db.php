<?php

$host = 'localhost';
$database = 'studenthub';
$username = 'root';
$dbPassword = '';

$conn = new mysqli($host, $username, $dbPassword, $database);

if ($conn->connect_error) {
    die('Database connection failed');
}

$conn->set_charset('utf8mb4');
