<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('127.0.0.1', 'root', '', 'practical13');
$conn->set_charset('utf8mb4');
