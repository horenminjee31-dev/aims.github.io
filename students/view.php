<?php
require_once __DIR__ . '/../includes/header.php';
$page_title = 'View Student';
$db = getDB();

$id = $_GET['id'] ?? 0;
$student = $db->prepare("SELECT s.*, c.class_name, g.group_name, sec.section_name, sh.shift_name, y.year as year_name 
    FROM students s 
    LEFT JOIN classes c ON s.class_id = c.id 
    LEFT JOIN class_groups g ON s.group_id = g.id 
    LEFT JOIN sections sec ON s.section_id = sec.id 
    LEFT JOIN shifts sh ON s.shift_id = sh.id 
    LEFT JOIN years y ON s.year_id = y.id 
    WHERE s.id = ?");
$student->execute([$id]);
$student = $student->fetch();

if (!$student) {
    setFlashMessage('error', 'Student not found');
    redirect(SITE_URL . '/students/search.php');
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user me-2"></i>Student Profile</h2>
        <div>
            <a href="search.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <a href="idcard.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-info"><i class="fas fa-id-card me-1"></i>ID Card</a>
            <a href="admitcard.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-warning"><i class="fas fa-file-alt me-1"></i>Admit Card</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <?php if ($student['photo']): ?>
                    <img src="<?php echo SITE_URL . '/' . $student['photo']; ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit:cover">
                    <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:150px;height:150px;font-size:60px"><?php echo strtoupper(substr($student['full_name'], 0, 1)); ?></div>
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($student['full_name']); ?></h4>
                    <p class="text-muted">ID: <?php echo $student['student_id']; ?></p>
                    <span class="badge bg-<?php echo $student['status'] === 'active' ? 'success' : 'danger'; ?> fs-6"><?php echo ucfirst($student['status']); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Academic Information</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><td width="30%"><strong>Class:</strong></td><td><?php echo $student['class_name']; ?> <?php echo $student['group_name'] ? '(' . $student['group_name'] . ')' : ''; ?></td></tr>
                        <tr><td><strong>Section:</strong></td><td><?php echo $student['section_name'] ?? '-'; ?></td></tr>
                        <tr><td><strong>Shift:</strong></td><td><?php echo $student['shift_name'] ?? '-'; ?></td></tr>
                        <tr><td><strong>Roll Number:</strong></td><td><?php echo $student['roll_number'] ?? '-'; ?></td></tr>
                        <tr><td><strong>Year:</strong></td><td><?php echo $student['year_name']; ?></td></tr>
                    </table>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header"><h5>Personal Information</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Gender:</strong> <?php echo ucfirst($student['gender']); ?></p>
                            <p><strong>Date of Birth:</strong> <?php echo $student['date_of_birth'] ? formatDate($student['date_of_birth']) : '-'; ?></p>
                            <p><strong>Blood Group:</strong> <?php echo $student['blood_group'] ?? '-'; ?></p>
                            <p><strong>Religion:</strong> <?php echo $student['religion'] ?? '-'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Father:</strong> <?php echo $student['father_name'] ?? '-'; ?></p>
                            <p><strong>Father's Phone:</strong> <?php echo $student['father_phone'] ?? '-'; ?></p>
                            <p><strong>Mother:</strong> <?php echo $student['mother_name'] ?? '-'; ?></p>
                            <p><strong>Mother's Phone:</strong> <?php echo $student['mother_phone'] ?? '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
