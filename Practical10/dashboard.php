<?php
$requiredRole = 'student';
require __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Portal</title>
    <link rel="stylesheet" href="CSS/dashboard.css">
</head>

<body>

    <nav>
        <a href="index.php" class="logo">
            <img src="Images/student_icon.png" alt="StudentHub">
            <span>StudentHub</span>
        </a>

        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="events.php">Events</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
            <button id="themeButton">
                <img src="Images/moon.png" alt="">
            </button>
        </div>
    </nav>


    <main class="dashboard">

        <div class="dashboard-grid">

            <a href="fee-payment.php" class="dashboard-card">
                <div class="card-content">
                    <h2>Fee Payment</h2>
                    <p>View your fees, payments and receipts.</p>
                    <span>View Details →</span>
                </div>
            </a>

            <a href="attendance.php" class="dashboard-card">
                <div class="card-content">
                    <h2>Attendance</h2>
                    <p>Check your subject-wise attendance.</p>
                    <span>View Details →</span>
                </div>
            </a>

            <a href="timetable.php" class="dashboard-card">
                <div class="card-content">
                    <h2>Timetable</h2>
                    <p>View your daily and weekly class schedule.</p>
                    <span>View Details →</span>
                </div>
            </a>

            <a href="results.php" class="dashboard-card">
                <div class="card-content">
                    <h2>Results</h2>
                    <p>Check your latest semester results.</p>
                    <span>View Details →</span>
                </div>
            </a>

        </div>

    </main>


    <footer>
        <p>&copy; 2026 StudentHub Portal. All rights reserved.</p>
    </footer>

    <script src="Js/dashboard.js"></script>
</body>
</html>
