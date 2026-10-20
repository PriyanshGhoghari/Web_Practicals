<?php

function h($value) { return htmlspecialchars((string)$value); }

$courses = ['BCA', 'BBA', 'BCom', 'BSc IT'];
$student = ['id' => 0, 'name' => '', 'email' => '', 'course' => 'BCA'];
$search = trim($_GET['search'] ?? '');
$filter = $_GET['course'] ?? '';
$error = '';
$students = [];
$messages = ['added' => 'Student added!', 'updated' => 'Student updated!', 'deleted' => 'Student deleted!'];
$message = $messages[$_GET['message'] ?? ''] ?? '';

try {
    require __DIR__ . '/db.php';

   
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);

        if (isset($_POST['delete'])) {
            $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            if ($stmt->affected_rows == 0) $error = 'Student not found.';
            else {
                header('Location: index.php?message=deleted');
                exit;
            }
        } else {
            $student = ['id' => $id, 'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''), 'course' => $_POST['course'] ?? ''];

            if ($student['name'] == '' || !filter_var($student['email'], FILTER_VALIDATE_EMAIL)
                || !in_array($student['course'], $courses)) {
                $error = 'Enter a name, valid email and course.';
            } else {
                $name = $student['name'];
                $email = $student['email'];
                $course = $student['course'];

                if ($id > 0) {
                    $stmt = $conn->prepare('SELECT id FROM students WHERE id = ?');
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    if (!$stmt->get_result()->fetch_assoc()) $error = 'Student not found.';
                    else {
                        $stmt = $conn->prepare('UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?');
                        $stmt->bind_param('sssi', $name, $email, $course, $id);
                        $message = 'updated';
                    }
                } else {
                    $stmt = $conn->prepare('INSERT INTO students (name, email, course) VALUES (?, ?, ?)');
                    $stmt->bind_param('sss', $name, $email, $course);
                    $message = 'added';
                }

                if (!$error) {
                    $stmt->execute();
                    header('Location: index.php?message=' . $message);
                    exit;
                }
            }
        }
    }


    if (isset($_GET['edit'])) {
        $id = (int)$_GET['edit'];
        $stmt = $conn->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $record = $stmt->get_result()->fetch_assoc();
        if ($record) $student = $record;
        else $error = 'Student not found.';
    }


    $keyword = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM students WHERE
        (name LIKE ? OR email LIKE ? OR course LIKE ?)
        AND (? = '' OR course = ?) ORDER BY id DESC");
    $stmt->bind_param('sssss', $keyword, $keyword, $keyword, $filter, $filter);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    $error = $e->getCode() == 1062 ? 'Email already exists!' : 'Database error. Check XAMPP and database.sql.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Practical 11 - Student Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h1>Student Management</h1>
    <p>Practical 11 — CRUD, Search and Filter</p>
    <?php if ($message): ?><p class="success"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>

    <section>
        <h2><?= $student['id'] ? 'Edit Student' : 'Add Student' ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= h($student['id']) ?>">
            <label>Name <input name="name" value="<?= h($student['name']) ?>" maxlength="100" required></label>
            <label>Email <input type="email" name="email" value="<?= h($student['email']) ?>" maxlength="150" required></label>
            <label>Course
                <select name="course">
                    <?php foreach ($courses as $item): ?>
                    <option value="<?= h($item) ?>" <?= $student['course'] == $item ? 'selected' : '' ?>><?= h($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit"><?= $student['id'] ? 'Update Student' : 'Add Student' ?></button>
            <?php if ($student['id']): ?><a href="index.php">Cancel</a><?php endif; ?>
        </form>
    </section>

    <section>
        <h2>Student Records</h2>
        <form method="get">
            <label>Search <input name="search" value="<?= h($search) ?>" placeholder="Name, email, course"></label>
            <label>Filter by Course
                <select name="course">
                    <option value="">All courses</option>
                    <?php foreach ($courses as $item): ?>
                    <option value="<?= h($item) ?>" <?= $filter == $item ? 'selected' : '' ?>><?= h($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Search / Filter</button>
            <a href="index.php">Reset</a>
        </form>
        <p><?= count($students) ?> students found</p>
        <div class="table-wrap">
            <table>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Actions</th></tr>
                <?php foreach ($students as $row): ?>
                <tr>
                    <td><?= h($row['id']) ?></td><td><?= h($row['name']) ?></td>
                    <td><?= h($row['email']) ?></td><td><?= h($row['course']) ?></td>
                    <td class="actions">
                        <a href="?edit=<?= h($row['id']) ?>">Edit</a>
                        <form method="post" onsubmit="return confirm('Delete this student?')">
                            <input type="hidden" name="id" value="<?= h($row['id']) ?>">
                            <button type="submit" name="delete" value="1" class="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$students): ?><tr><td colspan="5">No students found.</td></tr><?php endif; ?>
            </table>
        </div>
    </section>
</main>
</body>
</html>
