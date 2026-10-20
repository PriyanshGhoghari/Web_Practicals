<?php
function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function deletePoster($name)
{
    if ($name && basename($name) === $name && is_file(__DIR__ . '/uploads/' . $name)) {
        unlink(__DIR__ . '/uploads/' . $name);
    }
}

$statuses = ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'];
$event = ['id' => 0, 'title' => '', 'event_date' => '', 'venue' => '', 'status' => 'Upcoming', 'poster' => ''];
$events = [];
$error = '';
$newPoster = '';
$messages = ['added' => 'Event added!', 'updated' => 'Event updated!', 'deleted' => 'Event deleted!'];
$message = $messages[$_GET['msg'] ?? ''] ?? '';

try {
    require __DIR__ . '/db.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        throw new Exception('Upload too large. Select a poster under 2 MB.');
    }
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? $_GET['edit'] ?? 0);

    
    if ($id) {
        $stmt = $conn->prepare('SELECT * FROM events WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $event = $stmt->get_result()->fetch_assoc();
        if (!$event) throw new Exception('Event not found.');
    }

    
    if ($action === 'delete' && $id) {
        $stmt = $conn->prepare('DELETE FROM events WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        deletePoster($event['poster']);
        header('Location: index.php?msg=deleted');
        exit;
    }

    
    if ($action === 'save') {
        $oldPoster = $event['poster'];
        $event['title'] = trim($_POST['title'] ?? '');
        $event['event_date'] = $_POST['event_date'] ?? '';
        $event['venue'] = trim($_POST['venue'] ?? '');
        $event['status'] = $_POST['status'] ?? '';

        if (!$event['title'] || !$event['event_date'] || !$event['venue'] || !in_array($event['status'], $statuses)) {
            throw new Exception('Please fill in every field.');
        }

        $file = $_FILES['poster'] ?? null;
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload failed. Maximum poster size is 2 MB.');
            if ($file['size'] > 2 * 1024 * 1024) throw new Exception('Poster must be 2 MB or smaller.');

            $image = @getimagesize($file['tmp_name']);
            $types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!$image || !isset($types[$image['mime']])) throw new Exception('Only JPG and PNG images are allowed.');

            $newPoster = uniqid('poster_') . '.' . $types[$image['mime']];
            if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $newPoster)) {
                throw new Exception('Could not save poster. Check the uploads folder.');
            }
            $event['poster'] = $newPoster;
        }

        if ($id) {
            $stmt = $conn->prepare('UPDATE events SET title=?, event_date=?, venue=?, status=?, poster=? WHERE id=?');
            $stmt->bind_param('sssssi', $event['title'], $event['event_date'], $event['venue'], $event['status'], $event['poster'], $id);
            $result = 'updated';
        } else {
            $stmt = $conn->prepare('INSERT INTO events (title, event_date, venue, status, poster) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $event['title'], $event['event_date'], $event['venue'], $event['status'], $event['poster']);
            $result = 'added';
        }
        $stmt->execute();
        if ($newPoster) deletePoster($oldPoster);
        header('Location: index.php?msg=' . $result);
        exit;
    }
} catch (mysqli_sql_exception $e) {
    deletePoster($newPoster);
    $error = 'Database error. Start MySQL, import database.sql, and check db.php.';
} catch (Exception $e) {
    deletePoster($newPoster);
    $error = $e->getMessage();
}


if (isset($conn)) {
    try {
        $stmt = $conn->prepare('SELECT * FROM events ORDER BY id DESC');
        $stmt->execute();
        $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
        $error = 'Could not load events. Check the database setup.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Practical 12 - Event Management</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main>
        <h1>Practical 12: Event Management</h1>
        <p>Simple admin CRUD demo with poster uploads (JPG/PNG, maximum 2 MB).</p>
        <?php if ($message): ?><p class="success"><?= h($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>

        <section>
            <h2><?= $event['id'] ? 'Edit Event' : 'Add Event' ?></h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= h($event['id']) ?>">
                <div class="fields">
                    <label>Title <input name="title" maxlength="100" value="<?= h($event['title']) ?>" required></label>
                    <label>Date <input type="date" name="event_date" value="<?= h($event['event_date']) ?>" required></label>
                    <label>Venue <input name="venue" maxlength="100" value="<?= h($event['venue']) ?>" required></label>
                    <label>Status
                        <select name="status">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= h($status) ?>" <?= $event['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Poster (optional, JPG/PNG, max 2 MB) <input type="file" name="poster" accept=".jpg,.jpeg,.png"></label>
                </div>
                <?php if ($event['poster']): ?>
                    <p>Current poster: <a href="uploads/<?= h($event['poster']) ?>" target="_blank">View poster</a>. Leave upload empty to keep it.</p>
                <?php endif; ?>
                <p><button type="submit"><?= $event['id'] ? 'Update Event' : 'Add Event' ?></button> <a href="index.php">Cancel / Reset</a></p>
            </form>
        </section>

        <section>
            <h2>Event Records (<?= count($events) ?>)</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th>Poster</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $row): ?>
                            <tr>
                                <td><?= h($row['id']) ?></td>
                                <td><?= h($row['title']) ?></td>
                                <td><?= h($row['event_date']) ?></td>
                                <td><?= h($row['venue']) ?></td>
                                <td><?= h($row['status']) ?></td>
                                <td><?php if ($row['poster']): ?>
                                        <a href="uploads/<?= h($row['poster']) ?>" target="_blank"><img src="uploads/<?= h($row['poster']) ?>" alt="Event poster"></a>
                                        <?php else: ?>No poster<?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="index.php?edit=<?= h($row['id']) ?>">Edit</a>
                                    <form method="post" onsubmit="return confirm('Delete this event?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= h($row['id']) ?>">
                                        <button class="delete" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$events): ?><tr>
                                <td colspan="7">No events yet.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>