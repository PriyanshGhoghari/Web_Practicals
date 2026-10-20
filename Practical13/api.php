<?php
header('Content-Type: application/json');


function reply($success, $message, $students = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'students' => $students
    ]);
    exit;
}

try {
    require __DIR__ . '/db.php';

    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $conn->prepare('SELECT * FROM students ORDER BY id DESC');
        $stmt->execute();
        reply(true, 'Students loaded!', $stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        reply(false, 'Only GET and POST are allowed.');
    }

    $action = $_POST['action'] ?? '';

    
    if ($action === 'add' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $course = $_POST['course'] ?? '';

        if ($name === '' || strlen($name) > 100 || strlen($email) > 150 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !in_array($course, ['BCA', 'BBA', 'BCom', 'BSc IT'])) {
            reply(false, 'Enter a name, valid email and course.');
        }
    }

    if ($action === 'add') {
        $stmt = $conn->prepare('INSERT INTO students (name, email, course) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $course);
        $stmt->execute();
        reply(true, 'Student added!');
    }

    if ($action !== 'update' && $action !== 'delete') {
        reply(false, 'Invalid action.');
    }

    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) reply(false, 'Invalid student ID.');

    
    $stmt = $conn->prepare('SELECT id FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) reply(false, 'Student not found.');

    if ($action === 'update') {
        $stmt = $conn->prepare('UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?');
        $stmt->bind_param('sssi', $name, $email, $course, $id);
        $stmt->execute();
        reply(true, 'Student updated!');
    }

    $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    reply(true, 'Student deleted!');

} catch (mysqli_sql_exception $error) {
    if ($error->getCode() == 1062) reply(false, 'This email already exists!');
    reply(false, 'Database error. Check your XAMPP and database settings.');
}
