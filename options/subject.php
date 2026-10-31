<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Create Subject';

$db = getDB();

// Get classes for dropdown
$classes = $db->query("SELECT * FROM classes WHERE status = 'active' ORDER BY numeric_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $subject_name = trim($_POST['subject_name'] ?? '');
        $subject_name_bn = trim($_POST['subject_name_bn'] ?? '');
        $subject_code = trim($_POST['subject_code'] ?? '');
        $class_id = $_POST['class_id'] ?? null;
        $full_mark = $_POST['full_mark'] ?? 100;
        $pass_mark = $_POST['pass_mark'] ?? 33;
        
        if (empty($subject_name)) {
            setFlashMessage('error', 'Subject name is required');
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO subjects (subject_name, subject_name_bn, subject_code, class_id, full_mark, pass_mark) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$subject_name, $subject_name_bn, $subject_code, $class_id ?: null, $full_mark, $pass_mark]);
                setFlashMessage('success', 'Subject created successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $subject_name = trim($_POST['subject_name'] ?? '');
        $subject_name_bn = trim($_POST['subject_name_bn'] ?? '');
        $subject_code = trim($_POST['subject_code'] ?? '');
        $class_id = $_POST['class_id'] ?? null;
        $full_mark = $_POST['full_mark'] ?? 100;
        $pass_mark = $_POST['pass_mark'] ?? 33;
        
        try {
            $stmt = $db->prepare("UPDATE subjects SET subject_name = ?, subject_name_bn = ?, subject_code = ?, class_id = ?, full_mark = ?, pass_mark = ? WHERE id = ?");
            $stmt->execute([$subject_name, $subject_name_bn, $subject_code, $class_id ?: null, $full_mark, $pass_mark, $id]);
            setFlashMessage('success', 'Subject updated successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $db->prepare("DELETE FROM subjects WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Subject deleted successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    }
    redirect(SITE_URL . '/options/subject.php');
}

$subjects = $db->query("SELECT s.*, c.class_name FROM subjects s LEFT JOIN classes c ON s.class_id = c.id ORDER BY s.subject_name")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-book me-2"></i>Create Subject</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>Add New Subject
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr><th>#</th><th>Subject Name</th><th>Code</th><th>Class</th><th>Full Mark</th><th>Pass Mark</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $i => $s): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['subject_name']); ?><br><small class="text-muted"><?php echo htmlspecialchars($s['subject_name_bn']); ?></small></td>
                        <td><?php echo htmlspecialchars($s['subject_code']); ?></td>
                        <td><?php echo $s['class_name'] ?? 'All Classes'; ?></td>
                        <td><?php echo $s['full_mark']; ?></td>
                        <td><?php echo $s['pass_mark']; ?></td>
                        <td><span class="badge bg-<?php echo $s['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editSubject(<?php echo htmlspecialchars(json_encode($s)); ?>)"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="add">
        <div class="modal-header"><h5 class="modal-title">Add New Subject</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Subject Name *</label><input type="text" class="form-control" name="subject_name" required></div>
            <div class="mb-3"><label class="form-label">Subject Name (Bangla)</label><input type="text" class="form-control" name="subject_name_bn"></div>
            <div class="mb-3"><label class="form-label">Subject Code</label><input type="text" class="form-control" name="subject_code"></div>
            <div class="mb-3"><label class="form-label">Applicable Class</label><select class="form-select" name="class_id"><option value="">All Classes</option><?php foreach ($classes as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['class_name']; ?></option><?php endforeach; ?></select></div>
            <div class="row">
                <div class="col-6"><div class="mb-3"><label class="form-label">Full Mark</label><input type="number" class="form-control" name="full_mark" value="100" step="0.01"></div></div>
                <div class="col-6"><div class="mb-3"><label class="form-label">Pass Mark</label><input type="number" class="form-control" name="pass_mark" value="33" step="0.01"></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Subject</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Subject</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Subject Name *</label><input type="text" class="form-control" name="subject_name" id="edit_subject_name" required></div>
            <div class="mb-3"><label class="form-label">Subject Name (Bangla)</label><input type="text" class="form-control" name="subject_name_bn" id="edit_subject_name_bn"></div>
            <div class="mb-3"><label class="form-label">Subject Code</label><input type="text" class="form-control" name="subject_code" id="edit_subject_code"></div>
            <div class="mb-3"><label class="form-label">Applicable Class</label><select class="form-select" name="class_id" id="edit_class_id"><option value="">All Classes</option><?php foreach ($classes as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['class_name']; ?></option><?php endforeach; ?></select></div>
            <div class="row">
                <div class="col-6"><div class="mb-3"><label class="form-label">Full Mark</label><input type="number" class="form-control" name="full_mark" id="edit_full_mark" step="0.01"></div></div>
                <div class="col-6"><div class="mb-3"><label class="form-label">Pass Mark</label><input type="number" class="form-control" name="pass_mark" id="edit_pass_mark" step="0.01"></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update Subject</button></div>
    </form>
</div></div></div>

<script>function editSubject(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_subject_name').value=data.subject_name;document.getElementById('edit_subject_name_bn').value=data.subject_name_bn||'';document.getElementById('edit_subject_code').value=data.subject_code||'';document.getElementById('edit_class_id').value=data.class_id||'';document.getElementById('edit_full_mark').value=data.full_mark;document.getElementById('edit_pass_mark').value=data.pass_mark;new bootstrap.Modal(document.getElementById('editModal')).show();}</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
