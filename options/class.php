<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Create Class';

$db = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $class_name = trim($_POST['class_name'] ?? '');
        $class_name_bn = trim($_POST['class_name_bn'] ?? '');
        $numeric_name = $_POST['numeric_name'] ?? null;
        
        if (empty($class_name)) {
            setFlashMessage('error', 'Class name is required');
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO classes (class_name, class_name_bn, numeric_name) VALUES (?, ?, ?)");
                $stmt->execute([$class_name, $class_name_bn, $numeric_name]);
                setFlashMessage('success', 'Class created successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error creating class: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $class_name = trim($_POST['class_name'] ?? '');
        $class_name_bn = trim($_POST['class_name_bn'] ?? '');
        $numeric_name = $_POST['numeric_name'] ?? null;
        
        if (empty($class_name)) {
            setFlashMessage('error', 'Class name is required');
        } else {
            try {
                $stmt = $db->prepare("UPDATE classes SET class_name = ?, class_name_bn = ?, numeric_name = ? WHERE id = ?");
                $stmt->execute([$class_name, $class_name_bn, $numeric_name, $id]);
                setFlashMessage('success', 'Class updated successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error updating class: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $db->prepare("DELETE FROM classes WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Class deleted successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error deleting class: ' . $e->getMessage());
        }
    }
    
    redirect(SITE_URL . '/options/class.php');
}

// Get all classes
$classes = $db->query("SELECT * FROM classes ORDER BY numeric_name, class_name")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-graduation-cap me-2"></i>Create Class</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>Add New Class
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Class Name</th>
                            <th>Class Name (BN)</th>
                            <th>Numeric Value</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $index => $class): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_name_bn']); ?></td>
                            <td><?php echo htmlspecialchars($class['numeric_name']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $class['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($class['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($class['created_at']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editClass(<?php echo htmlspecialchars(json_encode($class)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="class_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Name (Bangla)</label>
                        <input type="text" class="form-control" name="class_name_bn">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numeric Value</label>
                        <input type="number" class="form-control" name="numeric_name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="class_name" id="edit_class_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Name (Bangla)</label>
                        <input type="text" class="form-control" name="class_name_bn" id="edit_class_name_bn">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numeric Value</label>
                        <input type="number" class="form-control" name="numeric_name" id="edit_numeric_name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editClass(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_class_name').value = data.class_name;
    document.getElementById('edit_class_name_bn').value = data.class_name_bn || '';
    document.getElementById('edit_numeric_name').value = data.numeric_name || '';
    
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
