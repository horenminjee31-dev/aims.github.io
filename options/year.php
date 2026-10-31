<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Create Year';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $year = $_POST['year'] ?? 0;
        $is_current = $_POST['is_current'] ?? 'no';
        
        if (empty($year)) {
            setFlashMessage('error', 'Year is required');
        } else {
            try {
                if ($is_current === 'yes') {
                    $db->query("UPDATE years SET is_current = 'no'");
                }
                $stmt = $db->prepare("INSERT INTO years (year, is_current) VALUES (?, ?)");
                $stmt->execute([$year, $is_current]);
                setFlashMessage('success', 'Year created successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $year = $_POST['year'] ?? 0;
        $is_current = $_POST['is_current'] ?? 'no';
        
        try {
            if ($is_current === 'yes') {
                $db->query("UPDATE years SET is_current = 'no'");
            }
            $stmt = $db->prepare("UPDATE years SET year = ?, is_current = ? WHERE id = ?");
            $stmt->execute([$year, $is_current, $id]);
            setFlashMessage('success', 'Year updated successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $db->prepare("DELETE FROM years WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Year deleted successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    } elseif ($action === 'toggle_status') {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'active';
        try {
            $stmt = $db->prepare("UPDATE years SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            setFlashMessage('success', 'Year status updated');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    }
    redirect(SITE_URL . '/options/year.php');
}

$years = $db->query("SELECT * FROM years ORDER BY year DESC")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt me-2"></i>Create Year</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>Add New Year
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr><th>#</th><th>Year</th><th>Current Year</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($years as $i => $y): ?>
                    <tr class="<?php echo $y['is_current'] === 'yes' ? 'table-success' : ''; ?>">
                        <td><?php echo $i + 1; ?></td>
                        <td><strong><?php echo $y['year']; ?></strong></td>
                        <td><?php echo $y['is_current'] === 'yes' ? '<span class="badge bg-primary"><i class="fas fa-check"></i> Current</span>' : '<span class="text-muted">-</span>'; ?></td>
                        <td><span class="badge bg-<?php echo $y['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($y['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editYear(<?php echo htmlspecialchars(json_encode($y)); ?>)"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $y['id']; ?>">
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
        <div class="modal-header"><h5 class="modal-title">Add New Year</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Year *</label><input type="number" class="form-control" name="year" value="<?php echo date('Y'); ?>" required></div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_current" value="yes" id="is_current">
                    <label class="form-check-label" for="is_current">Set as current year</label>
                </div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Year</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Year</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Year *</label><input type="number" class="form-control" name="year" id="edit_year" required></div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_current" value="yes" id="edit_is_current">
                    <label class="form-check-label" for="edit_is_current">Set as current year</label>
                </div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update Year</button></div>
    </form>
</div></div></div>

<script>function editYear(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_year').value=data.year;document.getElementById('edit_is_current').checked=data.is_current==='yes';new bootstrap.Modal(document.getElementById('editModal')).show();}</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
