<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();

$id = $_GET['id'] ?? 0;
$student = $db->prepare("SELECT s.*, c.class_name, sec.section_name FROM students s LEFT JOIN classes c ON s.class_id = c.id LEFT JOIN sections sec ON s.section_id = sec.id WHERE s.id = ?");
$student->execute([$id]);
$student = $student->fetch();

if (!$student) {
    die('Student not found');
}

$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ID Card - <?php echo $student['full_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; padding: 20px; }
        .id-card { width: 350px; height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); margin: 20px auto; color: white; position: relative; }
        .id-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/></svg>'); background-size: 100px; opacity: 0.3; }
        .id-header { padding: 15px; text-align: center; position: relative; z-index: 1; }
        .id-header h4 { margin: 0; font-size: 16px; font-weight: 700; }
        .id-header small { opacity: 0.9; font-size: 11px; }
        .id-body { display: flex; padding: 15px; position: relative; z-index: 1; }
        .id-photo { width: 80px; height: 80px; border-radius: 10px; border: 3px solid white; object-fit: cover; margin-right: 15px; background: white; }
        .id-info { flex: 1; font-size: 12px; }
        .id-info p { margin-bottom: 5px; }
        .id-info .label { opacity: 0.8; font-size: 10px; text-transform: uppercase; }
        .id-info .value { font-weight: 600; font-size: 13px; }
        .id-footer { position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 15px; background: rgba(0,0,0,0.2); font-size: 10px; text-align: center; }
        .print-btn { position: fixed; top: 20px; right: 20px; }
        @media print { body { background: white; padding: 0; } .print-btn { display: none; } .id-card { margin: 0; box-shadow: none; } }
    </style>
</head>
<body>
    <button class="btn btn-primary print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    
    <div class="id-card">
        <div class="id-header">
            <h4><?php echo $settings['school_name'] ?? 'SCHOOL NAME'; ?></h4>
            <small>Student Identity Card</small>
        </div>
        <div class="id-body">
            <?php if ($student['photo']): ?>
            <img src="<?php echo SITE_URL . '/' . $student['photo']; ?>" class="id-photo" alt="Photo">
            <?php else: ?>
            <div class="id-photo d-flex align-items-center justify-content-center bg-white text-dark" style="font-size: 30px;"><?php echo strtoupper(substr($student['full_name'], 0, 1)); ?></div>
            <?php endif; ?>
            <div class="id-info">
                <p><span class="label">Student Name</span><br><span class="value"><?php echo htmlspecialchars($student['full_name']); ?></span></p>
                <p><span class="label">ID No</span><br><span class="value"><?php echo $student['student_id']; ?></span></p>
                <p><span class="label">Class</span><br><span class="value"><?php echo $student['class_name']; ?> <?php echo $student['section_name'] ? '- ' . $student['section_name'] : ''; ?></span></p>
            </div>
        </div>
        <div class="id-footer">
            Valid for Academic Year <?php echo date('Y'); ?>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
