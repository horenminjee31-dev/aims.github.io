<?php require_once __DIR__ . '/../includes/header.php'; $page_title = 'Backup'; ?>
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-database me-2"></i>Database Backup</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-download fa-3x text-primary mb-3"></i>
                    <h4>Download Database Backup</h4>
                    <p class="text-muted">Create and download a backup of your database.</p>
                    <a href="?action=backup" class="btn btn-primary"><i class="fas fa-download me-1"></i>Create Backup</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
if (isset($_GET['action']) && $_GET['action'] === 'backup') {
    $db_name = DB_NAME;
    $filename = $db_name . '_' . date('Y-m-d_H-i-s') . '.sql';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $cmd = "mysqldump -h " . DB_HOST . " -u " . DB_USER . " " . (DB_PASS ? "-p" . DB_PASS . " " : "") . $db_name;
    passthru($cmd);
    exit;
}
require_once __DIR__ . '/../includes/footer.php'; 
?>
