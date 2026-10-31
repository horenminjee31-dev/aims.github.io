<?php
require_once __DIR__ . '/../includes/header.php';
$page_title = 'Create Exam Type';
$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $exam_name = trim($_POST['exam_name'] ?? '');
        $exam_name_bn = trim($_POST['exam_name_bn'] ?? '');
        if (empty($exam_name)) {
            setFlashMessage('error', 'Exam name is required');
        } else {
            $stmt = $db->prepare("INSERT INTO exam_types (exam_name, exam_name_bn) VALUES (?, ?)");
            $stmt->execute([$exam_name, $exam_name_bn]);
            setFlashMessage('success', 'Exam type created successfully');
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $stmt = $db->prepare("UPDATE exam_types SET exam_name = ?, exam_name_bn = ? WHERE id = ?");
        $stmt->execute([$_POST['exam_name'], $_POST['exam_name_bn'], $id]);
        setFlashMessage('success', 'Exam type updated successfully');
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM exam_types WHERE id = ?");
        $stmt->execute([$_POST['id'] ?? 0]);
        setFlashMessage('success', 'Exam type deleted successfully');
    }
    redirect(SITE_URL . '/options/examtype.php');
}
$exam_types = $db->query("SELECT * FROM exam_types ORDER BY exam_name")->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-alt me-2"></i>Create Exam Type</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i>Add New</button>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead><tr><th>#</th><th>Exam Name</th><th>Exam Name (BN)</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($exam_types as $i => $et): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($et['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($et['exam_name_bn']); ?></td>
                        <td><span class="badge bg-<?php echo $et['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($et['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editExamType(<?php echo htmlspecialchars(json_encode($et)); ?>)"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $et['id']; ?>">
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
        <div class="modal-header"><h5 class="modal-title">Add Exam Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Exam Name *</label><input type="text" class="form-control" name="exam_name" required></div>
            <div class="mb-3"><label class="form-label">Exam Name (Bangla)</label><input type="text" class="form-control" name="exam_name_bn"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Exam Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Exam Name *</label><input type="text" class="form-control" name="exam_name" id="edit_exam_name" required></div>
            <div class="mb-3"><label class="form-label">Exam Name (Bangla)</label><input type="text" class="form-control" name="exam_name_bn" id="edit_exam_name_bn"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
    </form>
</div></div></div>
<script>function editExamType(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_exam_name').value=data.exam_name;document.getElementById('edit_exam_name_bn').value=data.exam_name_bn||'';new bootstrap.Modal(document.getElementById('editModal')).show();}</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
