<?php
require_once __DIR__ . '/../includes/header.php';
$page_title = 'Create Designation';
$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO designations (designation_name, designation_name_bn, designation_type) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['designation_name'], $_POST['designation_name_bn'], $_POST['designation_type']]);
        setFlashMessage('success', 'Designation created successfully');
    } elseif ($action === 'edit') {
        $stmt = $db->prepare("UPDATE designations SET designation_name = ?, designation_name_bn = ?, designation_type = ? WHERE id = ?");
        $stmt->execute([$_POST['designation_name'], $_POST['designation_name_bn'], $_POST['designation_type'], $_POST['id']]);
        setFlashMessage('success', 'Designation updated successfully');
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM designations WHERE id = ?");
        $stmt->execute([$_POST['id'] ?? 0]);
        setFlashMessage('success', 'Designation deleted successfully');
    }
    redirect(SITE_URL . '/options/designation.php');
}
$designations = $db->query("SELECT * FROM designations ORDER BY designation_name")->fetchAll();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-id-badge me-2"></i>Create Designation</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i>Add Designation</button>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead><tr><th>#</th><th>Designation</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($designations as $i => $d): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($d['designation_name']); ?><br><small class="text-muted"><?php echo htmlspecialchars($d['designation_name_bn']); ?></small></td>
                        <td><span class="badge bg-<?php echo $d['designation_type'] === 'teacher' ? 'info' : 'secondary'; ?>"><?php echo ucfirst($d['designation_type']); ?></span></td>
                        <td><span class="badge bg-<?php echo $d['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($d['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editDesig(<?php echo htmlspecialchars(json_encode($d)); ?>)"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $d['id']; ?>">
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
        <div class="modal-header"><h5 class="modal-title">Add Designation</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Designation Name *</label><input type="text" class="form-control" name="designation_name" required></div>
            <div class="mb-3"><label class="form-label">Designation Name (Bangla)</label><input type="text" class="form-control" name="designation_name_bn"></div>
            <div class="mb-3"><label class="form-label">Type</label><select class="form-select" name="designation_type"><option value="teacher">Teacher</option><option value="staff">Staff</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Designation</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Designation Name *</label><input type="text" class="form-control" name="designation_name" id="edit_name" required></div>
            <div class="mb-3"><label class="form-label">Designation Name (Bangla)</label><input type="text" class="form-control" name="designation_name_bn" id="edit_name_bn"></div>
            <div class="mb-3"><label class="form-label">Type</label><select class="form-select" name="designation_type" id="edit_type"><option value="teacher">Teacher</option><option value="staff">Staff</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
    </form>
</div></div></div>
<script>function editDesig(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_name').value=data.designation_name;document.getElementById('edit_name_bn').value=data.designation_name_bn||'';document.getElementById('edit_type').value=data.designation_type;new bootstrap.Modal(document.getElementById('editModal')).show();}</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
