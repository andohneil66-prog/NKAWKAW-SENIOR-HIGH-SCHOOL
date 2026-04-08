<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$db   = 'nkawkaw_shs';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($action) {
    case 'login':
        handleLogin($pdo);
        break;
    case 'register':
        handleRegister($pdo);
        break;
    case 'adminLogin':
        handleAdminLogin($pdo);
        break;
    case 'students':
        handleStudents($pdo);
        break;
    case 'news':
        handleNews($pdo);
        break;
    case 'gallery':
        handleGallery($pdo);
        break;
    case 'results':
        handleResults($pdo);
        break;
    case 'uploadGallery':
        uploadGalleryImage($pdo);
        break;
    case 'publishNews':
        publishNewsItem($pdo);
        break;
    case 'uploadResults':
        uploadExamResults($pdo);
        break;
    default:
        echo json_encode(['status' => 'ok', 'message' => 'NKAWKAW SHS API']);
}

function handleLogin($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if ($username === 'student' && $password === '1234') {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => 'STU-DEMO',
                'name' => 'student',
                'class' => 'BS2',
                'track' => 'General Science'
            ]
        ]);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM students WHERE (id = ? OR email = ?) AND password = ?");
    $stmt->execute([$username, $username, $password]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $student['id'],
                'name' => $student['first_name'] . ' ' . $student['last_name'],
                'first_name' => $student['first_name'],
                'last_name' => $student['last_name'],
                'email' => $student['email'],
                'class' => $student['class'],
                'track' => $student['track'],
                'phone' => $student['phone']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }
}

function handleRegister($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email already registered']);
        return;
    }
    
    $countStmt = $pdo->query("SELECT COUNT(*) FROM students");
    $count = $countStmt->fetchColumn() + 1;
    $studentId = 'STU-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    $stmt = $pdo->prepare("INSERT INTO students (id, first_name, last_name, email, phone, class, track, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $studentId,
        $data['firstName'],
        $data['lastName'],
        $data['email'],
        $data['phone'],
        $data['class'],
        $data['track'],
        $data['password']
    ]);
    
    echo json_encode([
        'success' => true,
        'student' => [
            'id' => $studentId,
            'name' => $data['firstName'] . ' ' . $data['lastName']
        ]
    ]);
}

function handleAdminLogin($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE password = ?");
    $stmt->execute([$password]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo json_encode(['success' => true, 'admin' => $admin['username']]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid password']);
    }
}

function handleStudents($pdo) {
    $stmt = $pdo->query("SELECT id, first_name, last_name, class, track, status, created_at FROM students ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function handleNews($pdo) {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function publishNewsItem($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO news (title, category, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$data['title'], $data['category'], $data['content']]);
    
    echo json_encode(['success' => true]);
}

function handleGallery($pdo) {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function uploadGalleryImage($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $imageData = $data['image'] ?? '';
    
    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        $image = base64_decode(str_replace('data:image/' . $type[1] . ';base64,', '', $imageData));
        
        $filename = uniqid() . '.' . $type[1];
        $filepath = '../uploads/' . $filename;
        
        if (!is_dir('../uploads')) {
            mkdir('../uploads');
        }
        
        file_put_contents($filepath, $image);
        
        $stmt = $pdo->prepare("INSERT INTO gallery (filename, category, created_at) VALUES (?, 'general', NOW())");
        $stmt->execute([$filename]);
        
        echo json_encode(['success' => true, 'filename' => $filename]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image data']);
    }
}

function handleResults($pdo) {
    $studentId = $_GET['student_id'] ?? '';
    
    if ($studentId) {
        $stmt = $pdo->prepare("SELECT r.* FROM results r WHERE r.student_id = ? ORDER BY r.exam_date DESC");
        $stmt->execute([$studentId]);
    } else {
        $stmt = $pdo->query("SELECT r.*, s.first_name, s.last_name FROM results r LEFT JOIN students s ON r.student_id = s.id ORDER BY r.exam_date DESC");
    }
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function uploadExamResults($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO results (student_id, subject, score, grade, exam_type, exam_date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    foreach ($data['results'] as $result) {
        $stmt->execute([
            $result['student_id'],
            $result['subject'],
            $result['score'],
            $result['grade'],
            $data['exam_type'],
            $data['exam_date']
        ]);
    }
    
    echo json_encode(['success' => true]);
}