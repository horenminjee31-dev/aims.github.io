<?php
require_once __DIR__ . '/../includes/header.php';

$page_title = 'Create Section';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $section_name = trim($_POST['section_name'] ?? '');
        $section_name_bn = trim($_POST['section_name_bn'] ?? '');
        
        if (empty($section_name)) {
            setFlashMessage('error', 'Section name is required');
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO sections (section_name, section_name_bn) VALUES (?, ?)");
                $stmt->execute([$section_name, $section_name_bn]);
                setFlashMessage('success', 'Section created successfully');
            } catch (PDOException $e) {
                setFlashMessage('error', 'Error: ' . $e->getMessage());
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $section_name = trim($_POST['section_name'] ?? '');
        $section_name_bn = trim($_POST['section_name_bn'] ?? '');
        
        try {
            $stmt = $db->prepare("UPDATE sections SET section_name = ?, section_name_bn = ? WHERE id = ?");
            $stmt->execute([$section_name, $section_name_bn, $id]);
            setFlashMessage('success', 'Section updated successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $db->prepare("DELETE FROM sections WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'Section deleted successfully');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error: ' . $e->getMessage());
        }
    }
    redirect(SITE_URL . '/options/section.php');
}

$sections = $db->query("SELECT * FROM sections ORDER BY section_name")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-th-large me-2"></i>Create Section</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>Add New Section
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr><th>#</th><th>Section Name</th><th>Section Name (BN)</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $i => $s): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['section_name_bn']); ?></td>
                        <td><span class="badge bg-<?php echo $s['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editSection(<?php echo htmlspecialchars(json_encode($s)); ?>)"><i class="fas fa-edit"></i></button>
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
        <div class="modal-header"><h5 class="modal-title">Add New Section</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Section Name *</label><input type="text" class="form-control" name="section_name" required></div>
            <div class="mb-3"><label class="form-label">Section Name (Bangla)</label><input type="text" class="form-control" name="section_name_bn"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Section</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">Edit Section</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Section Name *</label><input type="text" class="form-control" name="section_name" id="edit_section_name" required></div>
            <div class="mb-3"><label class="form-label">Section Name (Bangla)</label><input type="text" class="form-control" name="section_name_bn" id="edit_section_name_bn"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update Section</button></div>
    </form>
</div></div></div>

<script>function editSection(data){document.getElementById('edit_id').value=data.id;document.getElementById('edit_section_name').value=data.section_name;document.getElementById('edit_section_name_bn').value=data.section_name_bn||'';new bootstrap.Modal(document.getElementById('editModal')).show();}</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
