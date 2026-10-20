<?php
require __DIR__ . '/auth.php';

$page = $_GET['page'] ?? 'home';
$isAdmin = $user['role'] === 'admin';
$error = '';
$message = isset($_GET['saved']) ? 'Event updated and added to the audit log.' : '';


if (!$isAdmin && ($page !== 'home' || $_SERVER['REQUEST_METHOD'] === 'POST')) {
    http_response_code(403);
    exit('Access denied. Admins only. <a href="index.php">Go Home</a>');
}


if ($isAdmin && $page === 'dashboard' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['event_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!in_array($status, ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'])) {
        $error = 'Please choose a valid status.';
    } else {
        try {
            $stmt = $conn->prepare('SELECT title, status FROM events WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $event = $stmt->get_result()->fetch_assoc();

            if (!$event) {
                $error = 'Event not found.';
            } elseif ($event['status'] === $status) {
                $error = 'This event already has that status.';
            } else {
                $stmt = $conn->prepare('UPDATE events SET status = ? WHERE id = ?');
                $stmt->bind_param('si', $status, $id);
                $stmt->execute();

                $action = 'Changed ' . $event['title'] . ' from ' . $event['status'] . ' to ' . $status;
                $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, action) VALUES (?, ?)');
                $stmt->bind_param('is', $user['id'], $action);
                $stmt->execute();

                header('Location: index.php?page=dashboard&saved=1');
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            $error = 'Could not update the event. Check the database.';
        }
    }
}


if ($page === 'dashboard') {
    $userCount = $conn->query('SELECT COUNT(*) FROM users')->fetch_row()[0];
    $studentCount = $conn->query('SELECT COUNT(*) FROM students')->fetch_row()[0];
    $eventCount = $conn->query('SELECT COUNT(*) FROM events')->fetch_row()[0];
    $registrationCount = $conn->query('SELECT COUNT(*) FROM registrations')->fetch_row()[0];

    $registrations = $conn->query('SELECT s.name, e.title, r.registered_at
        FROM registrations r
        JOIN students s ON s.id = r.student_id
        JOIN events e ON e.id = r.event_id
        ORDER BY r.registered_at DESC, r.id DESC LIMIT 5');

    $events = $conn->query('SELECT id, title, status FROM events ORDER BY id');
}

if ($page === 'audit') {
    $logs = $conn->query('SELECT u.name, a.action, a.created_at
        FROM audit_logs a
        JOIN users u ON u.id = a.user_id
        ORDER BY a.id DESC LIMIT 20');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Practical 14 - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h1>Practical 14 - Role-Based Dashboard</h1>
    <p>Logged in as <?= h($user['name']) ?> (<?= h($user['role']) ?>)</p>

    <nav>
        <a href="index.php">Home</a>
        <?php if ($isAdmin): ?>
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=audit">Audit Log</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </nav>

    <?php if ($page === 'home'): ?>
        <section>
            <h2>Home</h2>
            <p><?= $isAdmin ? 'You are an admin. Open the Dashboard or Audit Log from the menu.' : 'You are a student. Only Home and Logout are available.' ?></p>
        </section>
    <?php elseif ($page === 'dashboard'): ?>
        <h2>Dashboard</h2>
        <?php if ($message): ?><p class="success"><?= h($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>

        <div class="stats">
            <div><strong><?= $userCount ?></strong>Users</div>
            <div><strong><?= $studentCount ?></strong>Students</div>
            <div><strong><?= $eventCount ?></strong>Events</div>
            <div><strong><?= $registrationCount ?></strong>Registrations</div>
        </div>

        <section>
            <h2>Latest 5 Registrations</h2>
            <div class="table-wrap">
                <table>
                    <tr><th>Student</th><th>Event</th><th>Date</th></tr>
                    <?php while ($row = $registrations->fetch_assoc()): ?>
                        <tr><td><?= h($row['name']) ?></td><td><?= h($row['title']) ?></td><td><?= h($row['registered_at']) ?></td></tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </section>

        <section>
            <h2>Change Event Status (Admin Action)</h2>
            <div class="table-wrap">
                <table>
                    <tr><th>Event</th><th>Current Status</th><th>New Status</th></tr>
                    <?php while ($event = $events->fetch_assoc()): ?>
                        <tr>
                            <td><?= h($event['title']) ?></td>
                            <td><?= h($event['status']) ?></td>
                            <td>
                                <form method="post" action="index.php?page=dashboard">
                                    <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                                    <select name="status">
                                        <?php foreach (['Upcoming', 'Ongoing', 'Completed', 'Cancelled'] as $status): ?>
                                            <option value="<?= $status ?>" <?= $status === $event['status'] ? 'selected' : '' ?>><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button>Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </section>
    <?php elseif ($page === 'audit'): ?>
        <section>
            <h2>Audit Log - Latest 20 Actions</h2>
            <div class="table-wrap">
                <table>
                    <tr><th>Admin</th><th>Action</th><th>Time</th></tr>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr><td><?= h($log['name']) ?></td><td><?= h($log['action']) ?></td><td><?= h($log['created_at']) ?></td></tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
