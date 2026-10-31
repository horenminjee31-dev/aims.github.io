<?php
require_once __DIR__ . '/../config/database.php';
$db = getDB();

$id = $_GET['id'] ?? 0;
$exam_type = $_GET['exam'] ?? 'Final Examination';

$student = $db->prepare("SELECT s.*, c.class_name, sec.section_name FROM students s LEFT JOIN classes c ON s.class_id = c.id LEFT JOIN sections sec ON s.section_id = sec.id WHERE s.id = ?");
$student->execute([$id]);
$student = $student->fetch();

if (!$student) die('Student not found');
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Admit Card</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f0f0f0; padding: 20px; }
.admit-card { max-width: 500px; margin: 20px auto; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; }
.admit-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 25px; text-align: center; }
.admit-header h3 { margin: 0; font-size: 22px; font-weight: 700; }
.admit-header p { margin: 5px 0 0; opacity: 0.9; }
.admit-body { padding: 30px; }
.admit-photo { width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 3px solid #10b981; float: right; }
.info-row { margin-bottom: 15px; clear: both; }
.info-row::after { content: ''; clear: both; display: table; }
.info-label { font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase; }
.info-value { font-size: 16px; color: #111827; font-weight: 500; }
.exam-details { background: #f0fdf4; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #10b981; }
.rules { margin-top: 20px; padding-top: 20px; border-top: 1px dashed #e5e7eb; }
.rules ol { padding-left: 20px; font-size: 13px; color: #6b7280; }
.signature { margin-top: 30px; display: flex; justify-content: space-between; }
.sig-box { text-align: center; width: 150px; }
.sig-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 5px; font-size: 12px; }
.print-btn { position: fixed; top: 20px; right: 20px; }
@media print { body { background: white; padding: 0; } .print-btn { display: none; } .admit-card { box-shadow: none; margin: 0; } }
</style>
</head>
<body>
<button class="btn btn-success print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
<div class="admit-card">
    <div class="admit-header">
        <h3><?php echo $settings['school_name'] ?? 'SCHOOL NAME'; ?></h3>
        <p><?php echo $exam_type; ?> - Admit Card</p>
    </div>
    <div class="admit-body">
        <?php if ($student['photo']): ?><img src="<?php echo SITE_URL . '/' . $student['photo']; ?>" class="admit-photo"><?php endif; ?>
        <div class="info-row"><span class="info-label">Student Name</span><br><span class="info-value"><?php echo htmlspecialchars($student['full_name']); ?></span></div>
        <div class="info-row"><span class="info-label">Student ID</span><br><span class="info-value"><?php echo $student['student_id']; ?></span></div>
        <div class="info-row"><span class="info-label">Class / Roll</span><br><span class="info-value"><?php echo $student['class_name']; ?> / Roll: <?php echo $student['roll_number']; ?></span></div>
        <div class="info-row"><span class="info-label">Father's Name</span><br><span class="info-value"><?php echo $student['father_name'] ?? 'N/A'; ?></span></div>
        <div class="exam-details">
            <div class="row">
                <div class="col-6"><span class="info-label">Examination</span><br><span class="info-value"><?php echo $exam_type; ?></span></div>
                <div class="col-6"><span class="info-label">Academic Year</span><br><span class="info-value"><?php echo date('Y'); ?></span></div>
            </div>
        </div>
        <div class="rules">
            <h6><i class="fas fa-exclamation-circle text-warning me-1"></i>Rules & Regulations:</h6>
            <ol>
                <li>Student must bring this admit card to the examination hall.</li>
                <li>No electronic devices allowed in the examination hall.</li>
                <li>Student should arrive 30 minutes before the exam starts.</li>
                <li>Any kind of malpractice is strictly prohibited.</li>
            </ol>
        </div>
        <div class="signature">
            <div class="sig-box"><div class="sig-line">Student Signature</div></div>
            <div class="sig-box"><div class="sig-line">Principal Signature</div></div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body></html>
