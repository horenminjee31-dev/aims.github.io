<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Create Class Time';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $period_name = trim($_POST['period_name'] ?? '');
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        
        if (empty($period_name) || empty($start_time) || empty($end_time)) {
            setFlashMessage('error', 'All fields are required');
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO class_times (period_name, start_time, end_time) VALUES (?, ?, ?)");
                $stmt->execute([$period_name, $start_time, $end_time]);
                setFlashMessage('success', 'Class time created successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $period_name = trim($_POST['period_name'] ?? '');
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        
        try {
            $stmt = $db->prepare("UPDATE class_times SET period_name = ?, start_time = ?, end_time = ? WHERE id = ?");
            $stmt->execute([$period_name, $start_time, $end_time, $id]);
            setFlashMessage('success', 'Class time updated successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $db->prepare("DELETE FROM class_times WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Class time deleted successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    }
    redirect(SITE_URL . '/options/classtime.php');
}

$class_times = $db->query("SELECT * FROM class_times ORDER BY start_time")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clock me-2"></i>Create Class Time</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>Add Class Time
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr><th>#</th><th>Period Name</th><th>Start Time</th><th>End Time</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($class_times as $i => $ct): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($ct['period_name']); ?></td>
                        <td><?php echo date('h:i A', strtotime($ct['start_time'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($ct['end_time'])); ?></td>
                        <td><span class="badge bg-<?php echo $ct['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($ct['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editClassTime(<?php echo htmlspecialchars(json_encode($ct)); ?>)"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $ct['id']; ?>">
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
        <div class="modal-header"><h5 class="modal-title">Add Class Time</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Period Name *</label><input type="text" class="form-control" name="period_name" placeholder="e.g., 1st Period" required></div>
            <div class="row">
                <div class="col-6"><div class="mb-3"><label class="form-label">Start Time *</label><input type="time" class="form-control" name="start_time" required></div></div>
                <div class="col-6"><div class="mb-3"><label class="form-label">End Time *</label><input type="time" class="form-control" name="end_time" required></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Class Time</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Period Name *</label><input type="text" class="form-control" name="period_name" id="edit_period_name" required></div>
            <div class="row">
                <div class="col-6"><div class="mb-3"><label class="form-label">Start Time *</label><input type="time" class="form-control" name="start_time" id="edit_start_time" required></div></div>
                <div class="col-6"><div class="mb-3"><label class="form-label">End Time *</label><input type="time" class="form-control" name="end_time" id="edit_end_time" required></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
    </form>
</div></div></div>

<script>function editClassTime(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_period_name').value=data.period_name;document.getElementById('edit_start_time').value=data.start_time;document.getElementById('edit_end_time').value=data.end_time;new bootstrap.Modal(document.getElementById('editModal')).show();}</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
