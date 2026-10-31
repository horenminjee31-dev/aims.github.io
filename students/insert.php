<?php
require_once __DIR__ . '/../includes/header.php';
$page_title = 'Insert Student Info';
$db = getDB();

// Get dropdown data
$classes = $db->query("SELECT * FROM classes WHERE status = 'active' ORDER BY numeric_name")->fetchAll();
$groups = $db->query("SELECT * FROM class_groups WHERE status = 'active'")->fetchAll();
$sections = $db->query("SELECT * FROM sections WHERE status = 'active'")->fetchAll();
$shifts = $db->query("SELECT * FROM shifts WHERE status = 'active'")->fetchAll();
$years = $db->query("SELECT * FROM years WHERE status = 'active' ORDER BY year DESC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = generateId('STU', 'students', 'student_id');
    $data = [
        $student_id,
        $_POST['class_id'],
        $_POST['group_id'] ?: null,
        $_POST['section_id'] ?: null,
        $_POST['shift_id'] ?: null,
        $_POST['year_id'],
        $_POST['roll_number'],
        $_POST['full_name'],
        $_POST['full_name_bn'],
        $_POST['gender'],
        $_POST['date_of_birth'] ?: null,
        $_POST['blood_group'],
        $_POST['religion'],
        $_POST['father_name'],
        $_POST['father_name_bn'],
        $_POST['father_phone'],
        $_POST['father_occupation'],
        $_POST['mother_name'],
        $_POST['mother_name_bn'],
        $_POST['mother_phone'],
        $_POST['mother_occupation'],
        $_POST['guardian_name'],
        $_POST['guardian_phone'],
        $_POST['guardian_relation'],
        $_POST['present_address'],
        $_POST['permanent_address'],
        $_POST['previous_school'],
        $_POST['previous_class']
    ];
    
    // Handle photo upload
    $photo = null;
    if (!empty($_FILES['photo']['tmp_name'])) {
        $upload = uploadFile($_FILES['photo'], 'uploads/students/');
        if ($upload['success']) {
            $photo = $upload['filename'];
        }
    }
    
    try {
        $stmt = $db->prepare("INSERT INTO students (student_id, class_id, group_id, section_id, shift_id, year_id, roll_number, full_name, full_name_bn, gender, date_of_birth, blood_group, religion, father_name, father_name_bn, father_phone, father_occupation, mother_name, mother_name_bn, mother_phone, mother_occupation, guardian_name, guardian_phone, guardian_relation, present_address, permanent_address, previous_school, previous_class, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_merge($data, [$photo]));
        setFlashMessage('success', 'Student added successfully. Student ID: ' . $student_id);
        redirect(SITE_URL . '/students/insert.php');
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error: ' . $e->getMessage());
    }
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-plus me-2"></i>Insert Student Information</h2>
        <a href="<?php echo SITE_URL; ?>/students/search.php" class="btn btn-secondary"><i class="fas fa-search me-1"></i>Search Students</a>
    </div>
    <form method="POST" enctype="multipart/form-data" class="form-container">
        <div class="row">
            <!-- Academic Info -->
            <div class="col-md-12 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-school me-2"></i>Academic Information</h5>
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label">Class *</label><select class="form-select" name="class_id" required><option value="">Select Class</option><?php foreach ($classes as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['class_name']; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Group</label><select class="form-select" name="group_id"><option value="">Select Group</option><?php foreach ($groups as $g): ?><option value="<?php echo $g['id']; ?>"><?php echo $g['group_name']; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Section</label><select class="form-select" name="section_id"><option value="">Select Section</option><?php foreach ($sections as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo $s['section_name']; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Shift</label><select class="form-select" name="shift_id"><option value="">Select Shift</option><?php foreach ($shifts as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo $s['shift_name']; ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label">Year *</label><select class="form-select" name="year_id" required><option value="">Select Year</option><?php foreach ($years as $y): ?><option value="<?php echo $y['id']; ?>" <?php echo $y['is_current'] === 'yes' ? 'selected' : ''; ?>><?php echo $y['year']; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Roll Number</label><input type="number" class="form-control" name="roll_number"></div>
                </div>
            </div>
            
            <!-- Personal Info -->
            <div class="col-md-12 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Full Name *</label><input type="text" class="form-control" name="full_name" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Full Name (Bangla)</label><input type="text" class="form-control" name="full_name_bn"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Photo</label><input type="file" class="form-control" name="photo" accept="image/*"></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label">Gender *</label><select class="form-select" name="gender" required><option value="">Select</option><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Date of Birth</label><input type="date" class="form-control" name="date_of_birth"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Blood Group</label><select class="form-select" name="blood_group"><option value="">Select</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Religion</label><input type="text" class="form-control" name="religion" value="Islam"></div>
                </div>
            </div>
            
            <!-- Guardian Info -->
            <div class="col-md-12 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-users me-2"></i>Guardian Information</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Father's Name</label><input type="text" class="form-control" name="father_name"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Father's Name (Bangla)</label><input type="text" class="form-control" name="father_name_bn"></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Father's Phone</label><input type="text" class="form-control" name="father_phone"></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Occupation</label><input type="text" class="form-control" name="father_occupation"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Mother's Name</label><input type="text" class="form-control" name="mother_name"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Mother's Name (Bangla)</label><input type="text" class="form-control" name="mother_name_bn"></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Mother's Phone</label><input type="text" class="form-control" name="mother_phone"></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Occupation</label><input type="text" class="form-control" name="mother_occupation"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Guardian Name</label><input type="text" class="form-control" name="guardian_name"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Guardian Phone</label><input type="text" class="form-control" name="guardian_phone"></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Relation</label><input type="text" class="form-control" name="guardian_relation"></div>
                </div>
            </div>
            
            <!-- Address -->
            <div class="col-md-12 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address</h5>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Present Address</label><textarea class="form-control" name="present_address" rows="2"></textarea></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Permanent Address</label><textarea class="form-control" name="permanent_address" rows="2"></textarea></div>
                </div>
            </div>
            
            <!-- Previous Education -->
            <div class="col-md-12 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-history me-2"></i>Previous Education</h5>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Previous School</label><input type="text" class="form-control" name="previous_school"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Previous Class</label><input type="text" class="form-control" name="previous_class"></div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Save Student</button>
            <button type="reset" class="btn btn-secondary btn-lg ms-2"><i class="fas fa-redo me-2"></i>Reset</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
