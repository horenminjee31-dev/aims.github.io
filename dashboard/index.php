<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Dashboard';

// Get statistics
$db = getDB();

$stats = [
    'students' => $db->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn(),
    'teachers' => $db->query("SELECT COUNT(*) FROM teachers WHERE status = 'active'")->fetchColumn(),
    'staff' => $db->query("SELECT COUNT(*) FROM staff WHERE status = 'active'")->fetchColumn(),
    'classes' => $db->query("SELECT COUNT(*) FROM classes WHERE status = 'active'")->fetchColumn(),
    'subjects' => $db->query("SELECT COUNT(*) FROM subjects WHERE status = 'active'")->fetchColumn(),
    'books' => $db->query("SELECT COUNT(*) FROM books WHERE status = 'available'")->fetchColumn(),
];

// Get today's attendance
$today = date('Y-m-d');
$student_attendance = $db->prepare("SELECT 
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
    COUNT(*) as total
FROM student_attendance WHERE date = ?");
$student_attendance->execute([$today]);
$attendance = $student_attendance->fetch();

// Recent students
$recent_students = $db->query("SELECT s.*, c.class_name 
    FROM students s 
    LEFT JOIN classes c ON s.class_id = c.id 
    ORDER BY s.created_at DESC LIMIT 5")->fetchAll();

// Fee collection this month
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$fee_collection = $db->prepare("SELECT SUM(total_paid) FROM fee_collections WHERE payment_date BETWEEN ? AND ?");
$fee_collection->execute([$month_start, $month_end]);
$monthly_fees = $fee_collection->fetchColumn() ?: 0;
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <div>
            <span id="live-date" class="text-muted me-3"></span>
            <span id="live-clock" class="badge bg-primary fs-6"></span>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="dashboard-card text-center fade-in">
                <div class="icon bg-primary-light mx-auto">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3><?php echo number_format($stats['students']); ?></h3>
                <p>Total Students</p>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="dashboard-card text-center fade-in" style="animation-delay: 0.1s">
                <div class="icon bg-success-light mx-auto">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3><?php echo number_format($stats['teachers']); ?></h3>
                <p>Total Teachers</p>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="dashboard-card text-center fade-in" style="animation-delay: 0.2s">
                <div class="icon bg-warning-light mx-auto">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo number_format($stats['staff']); ?></h3>
                <p>Total Staff</p>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="dashboard-card text-center fade-in" style="animation-delay: 0.3s">
                <div class="icon bg-info-light mx-auto">
                    <i class="fas fa-school"></i>
                </div>
                <h3><?php echo number_format($stats['classes']); ?></h3>
                <p>Total Classes</p>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="dashboard-card text-center fade-in" style="animation-delay: 0.4s">
                <div class="icon bg-danger-light mx-auto">
                    <i class="fas fa-book"></i>
                </div>
                <h3><?php echo number_format($stats['subjects']); ?></h3>
                <p>Total Subjects</p>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="dashboard-card text-center fade-in" style="animation-delay: 0.5s">
                <div class="icon bg-success-light mx-auto">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h3><?php echo number_format($stats['books']); ?></h3>
                <p>Available Books</p>
            </div>
        </div>
    </div>
    
    <!-- Second Row -->
    <div class="row mt-4">
        <!-- Today's Attendance -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Today's Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h4 class="text-success mb-1"><?php echo $attendance['present'] ?? 0; ?></h4>
                                <small class="text-muted">Present</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <h4 class="text-danger mb-1"><?php echo $attendance['absent'] ?? 0; ?></h4>
                                <small class="text-muted">Absent</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <h4 class="text-info mb-1"><?php echo $attendance['total'] ?? 0; ?></h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/attendance/student.php" class="btn btn-outline-primary btn-sm w-100 mt-3">
                        <i class="fas fa-plus me-1"></i>Take Attendance
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Monthly Fee Collection -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Fee Collection (<?php echo date('F Y'); ?>)</h5>
                </div>
                <div class="card-body text-center">
                    <div class="stats-card mb-3">
                        <h3>৳<?php echo number_format($monthly_fees, 2); ?></h3>
                        <p>Total Collected This Month</p>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/accounts/fee_collection.php" class="btn btn-outline-light btn-sm w-100">
                        <i class="fas fa-plus me-1"></i>Collect Fee
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/students/insert.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i>Add New Student
                        </a>
                        <a href="<?php echo SITE_URL; ?>/teachers/insert.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-user-plus me-1"></i>Add New Teacher
                        </a>
                        <a href="<?php echo SITE_URL; ?>/marks/input.php" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-edit me-1"></i>Input Marks
                        </a>
                        <a href="<?php echo SITE_URL; ?>/result/publish.php" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-bullhorn me-1"></i>Publish Result
                        </a>
                        <a href="<?php echo SITE_URL; ?>/sms/create.php" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sms me-1"></i>Send SMS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Students -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recently Added Students</h5>
                    <a href="<?php echo SITE_URL; ?>/students/search.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Roll No</th>
                                    <th>Guardian Phone</th>
                                    <th>Added On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($student['photo']): ?>
                                            <img src="<?php echo SITE_URL . '/' . $student['photo']; ?>" class="rounded-circle me-2" width="35" height="35" alt="">
                                            <?php else: ?>
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px">
                                                <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($student['full_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['guardian_phone']); ?></td>
                                    <td><?php echo formatDate($student['created_at']); ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/students/view.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recent_students)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No students found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
