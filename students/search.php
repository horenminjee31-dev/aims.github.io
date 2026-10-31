<?php
require_once __DIR__ . '/../includes/header.php';
$page_title = 'Search Student';
$db = getDB();

$classes = $db->query("SELECT * FROM classes WHERE status = 'active'")->fetchAll();
$students = [];

if (isset($_GET['search'])) {
    $sql = "SELECT s.*, c.class_name, g.group_name, sec.section_name, sh.shift_name, y.year as year_name 
            FROM students s 
            LEFT JOIN classes c ON s.class_id = c.id 
            LEFT JOIN class_groups g ON s.group_id = g.id 
            LEFT JOIN sections sec ON s.section_id = sec.id 
            LEFT JOIN shifts sh ON s.shift_id = sh.id 
            LEFT JOIN years y ON s.year_id = y.id 
            WHERE 1=1";
    $params = [];
    
    if (!empty($_GET['class_id'])) {
        $sql .= " AND s.class_id = ?";
        $params[] = $_GET['class_id'];
    }
    if (!empty($_GET['student_id'])) {
        $sql .= " AND s.student_id LIKE ?";
        $params[] = '%' . $_GET['student_id'] . '%';
    }
    if (!empty($_GET['name'])) {
        $sql .= " AND s.full_name LIKE ?";
        $params[] = '%' . $_GET['name'] . '%';
    }
    if (!empty($_GET['roll'])) {
        $sql .= " AND s.roll_number = ?";
        $params[] = $_GET['roll'];
    }
    
    $sql .= " ORDER BY s.id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
}
?>
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-search me-2"></i>Search Student</h2>
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label">Class</label><select class="form-select" name="class_id"><option value="">All Classes</option><?php foreach ($classes as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($_GET['class_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo $c['class_name']; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Student ID</label><input type="text" class="form-control" name="student_id" value="<?php echo $_GET['student_id'] ?? ''; ?>"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Name</label><input type="text" class="form-control" name="name" value="<?php echo $_GET['name'] ?? ''; ?>"></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Roll No</label><input type="text" class="form-control" name="roll" value="<?php echo $_GET['roll'] ?? ''; ?>"></div>
                    <div class="col-md-1 mb-3 d-flex align-items-end"><button type="submit" name="search" class="btn btn-primary w-100"><i class="fas fa-search"></i></button></div>
                </div>
            </form>
        </div>
    </div>
    <?php if (isset($_GET['search'])): ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Search Results (<?php echo count($students); ?> found)</span>
            <div>
                <a href="<?php echo SITE_URL; ?>/students/insert.php" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add New</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Photo</th><th>Student ID</th><th>Name</th><th>Class</th><th>Roll</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php if ($s['photo']): ?><img src="<?php echo SITE_URL . '/' . $s['photo']; ?>" width="40" height="40" class="rounded-circle"><?php else: ?><div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px"><?php echo strtoupper(substr($s['full_name'], 0, 1)); ?></div><?php endif; ?></td>
                            <td><?php echo $s['student_id']; ?></td>
                            <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                            <td><?php echo $s['class_name']; ?> <?php echo $s['section_name'] ? '- ' . $s['section_name'] : ''; ?></td>
                            <td><?php echo $s['roll_number']; ?></td>
                            <td><?php echo $s['guardian_phone']; ?></td>
                            <td><span class="badge bg-<?php echo $s['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                            <td>
                                <a href="view.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="idcard.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-secondary" target="_blank"><i class="fas fa-id-card"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
