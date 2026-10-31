<?php
require_once __DIR__ . '/../includes/header.php';
$page_title = 'Create Exam Grade';
$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO exam_grades (grade_name, min_mark, max_mark, grade_point) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['grade_name'], $_POST['min_mark'], $_POST['max_mark'], $_POST['grade_point']]);
        setFlashMessage('success', 'Grade created successfully');
    } elseif ($action === 'edit') {
        $stmt = $db->prepare("UPDATE exam_grades SET grade_name = ?, min_mark = ?, max_mark = ?, grade_point = ? WHERE id = ?");
        $stmt->execute([$_POST['grade_name'], $_POST['min_mark'], $_POST['max_mark'], $_POST['grade_point'], $_POST['id']]);
        setFlashMessage('success', 'Grade updated successfully');
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM exam_grades WHERE id = ?");
        $stmt->execute([$_POST['id'] ?? 0]);
        setFlashMessage('success', 'Grade deleted successfully');
    }
    redirect(SITE_URL . '/options/examgrade.php');
}
$grades = $db->query("SELECT * FROM exam_grades ORDER BY min_mark DESC")->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-star me-2"></i>Create Exam Grade</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i>Add Grade</button>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead><tr><th>#</th><th>Grade</th><th>Min Mark</th><th>Max Mark</th><th>Grade Point</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($grades as $i => $g): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><span class="badge bg-primary fs-6"><?php echo htmlspecialchars($g['grade_name']); ?></span></td>
                        <td><?php echo $g['min_mark']; ?></td>
                        <td><?php echo $g['max_mark']; ?></td>
                        <td><?php echo $g['grade_point']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editGrade(<?php echo htmlspecialchars(json_encode($g)); ?>)"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $g['id']; ?>">
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
        <div class="modal-header"><h5 class="modal-title">Add Grade</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Grade Name *</label><input type="text" class="form-control" name="grade_name" placeholder="A+, A, A-" required></div>
            <div class="row">
                <div class="col-4"><div class="mb-3"><label class="form-label">Min Mark *</label><input type="number" class="form-control" name="min_mark" step="0.01" required></div></div>
                <div class="col-4"><div class="mb-3"><label class="form-label">Max Mark *</label><input type="number" class="form-control" name="max_mark" step="0.01" required></div></div>
                <div class="col-4"><div class="mb-3"><label class="form-label">Grade Point *</label><input type="number" class="form-control" name="grade_point" step="0.01" required></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Grade</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Grade Name *</label><input type="text" class="form-control" name="grade_name" id="edit_grade_name" required></div>
            <div class="row">
                <div class="col-4"><div class="mb-3"><label class="form-label">Min Mark *</label><input type="number" class="form-control" name="min_mark" id="edit_min_mark" step="0.01" required></div></div>
                <div class="col-4"><div class="mb-3"><label class="form-label">Max Mark *</label><input type="number" class="form-control" name="max_mark" id="edit_max_mark" step="0.01" required></div></div>
                <div class="col-4"><div class="mb-3"><label class="form-label">Grade Point *</label><input type="number" class="form-control" name="grade_point" id="edit_grade_point" step="0.01" required></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
    </form>
</div></div></div>
<script>function editGrade(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_grade_name').value=data.grade_name;document.getElementById('edit_min_mark').value=data.min_mark;document.getElementById('edit_max_mark').value=data.max_mark;document.getElementById('edit_grade_point').value=data.grade_point;new bootstrap.Modal(document.getElementById('editModal')).show();}</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
