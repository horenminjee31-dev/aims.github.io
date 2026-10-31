<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Create Shift';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $shift_name = trim($_POST['shift_name'] ?? '');
        $shift_name_bn = trim($_POST['shift_name_bn'] ?? '');
        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        
        if (empty($shift_name)) {
            setFlashMessage('error', 'Shift name is required');
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO shifts (shift_name, shift_name_bn, start_time, end_time) VALUES (?, ?, ?, ?)");
                $stmt->execute([$shift_name, $shift_name_bn, $start_time, $end_time]);
                setFlashMessage('success', 'Shift created successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $shift_name = trim($_POST['shift_name'] ?? '');
        $shift_name_bn = trim($_POST['shift_name_bn'] ?? '');
        $start_time = $_POST['start_time'] ?? null;
        $end_time = $_POST['end_time'] ?? null;
        
        try {
            $stmt = $db->prepare("UPDATE shifts SET shift_name = ?, shift_name_bn = ?, start_time = ?, end_time = ? WHERE id = ?");
            $stmt->execute([$shift_name, $shift_name_bn, $start_time, $end_time, $id]);
            setFlashMessage('success', 'Shift updated successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $db->prepare("DELETE FROM shifts WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Shift deleted successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    }
    redirect(SITE_URL . '/options/shift.php');
}

$shifts = $db->query("SELECT * FROM shifts ORDER BY shift_name")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clock me-2"></i>Create Shift</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>Add New Shift
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr><th>#</th><th>Shift Name</th><th>Shift Name (BN)</th><th>Start Time</th><th>End Time</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($shifts as $i => $s): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['shift_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['shift_name_bn']); ?></td>
                        <td><?php echo $s['start_time'] ? date('h:i A', strtotime($s['start_time'])) : '-'; ?></td>
                        <td><?php echo $s['end_time'] ? date('h:i A', strtotime($s['end_time'])) : '-'; ?></td>
                        <td><span class="badge bg-<?php echo $s['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editShift(<?php echo htmlspecialchars(json_encode($s)); ?>)"><i class="fas fa-edit"></i></button>
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
        <div class="modal-header"><h5 class="modal-title">Add New Shift</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Shift Name *</label><input type="text" class="form-control" name="shift_name" required></div>
            <div class="mb-3"><label class="form-label">Shift Name (Bangla)</label><input type="text" class="form-control" name="shift_name_bn"></div>
            <div class="row">
                <div class="col-6"><div class="mb-3"><label class="form-label">Start Time</label><input type="time" class="form-control" name="start_time"></div></div>
                <div class="col-6"><div class="mb-3"><label class="form-label">End Time</label><input type="time" class="form-control" name="end_time"></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Shift</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Shift</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Shift Name *</label><input type="text" class="form-control" name="shift_name" id="edit_shift_name" required></div>
            <div class="mb-3"><label class="form-label">Shift Name (Bangla)</label><input type="text" class="form-control" name="shift_name_bn" id="edit_shift_name_bn"></div>
            <div class="row">
                <div class="col-6"><div class="mb-3"><label class="form-label">Start Time</label><input type="time" class="form-control" name="start_time" id="edit_start_time"></div></div>
                <div class="col-6"><div class="mb-3"><label class="form-label">End Time</label><input type="time" class="form-control" name="end_time" id="edit_end_time"></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update Shift</button></div>
    </form>
</div></div></div>

<script>function editShift(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_shift_name').value=data.shift_name;document.getElementById('edit_shift_name_bn').value=data.shift_name_bn||'';document.getElementById('edit_start_time').value=data.start_time||'';document.getElementById('edit_end_time').value=data.end_time||'';new bootstrap.Modal(document.getElementById('editModal')).show();}</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
