<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    redirect(SITE_URL . '/auth/login.php');
}

// Get current settings
$db = getDB();
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . ($settings['school_name'] ?? SITE_NAME) : ($settings['school_name'] ?? SITE_NAME); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php if (isLoggedIn()): ?>
        <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-school"></i> <?php echo $settings['school_name'] ?? 'School'; ?></h3>
            </div>
            
            <ul class="list-unstyled components">
                <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                    <a href="<?php echo SITE_URL; ?>/dashboard/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <!-- Create Options -->
                <li>
                    <a href="#createSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-plus-circle"></i> Create Options
                    </a>
                    <ul class="collapse list-unstyled submenu" id="createSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/options/class.php">Create Class</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/group.php">Create Group</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/section.php">Create Section</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/shift.php">Create Shift</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/classtime.php">Create Class Time</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/year.php">Create Year</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/subject.php">Create Subject</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/examtype.php">Create Exam Type</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/examgrade.php">Create Exam Grade</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/options/designation.php">Create Designation</a></li>
                    </ul>
                </li>
                
                <!-- Student Management -->
                <li>
                    <a href="#studentSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-user-graduate"></i> Student Management
                    </a>
                    <ul class="collapse list-unstyled submenu" id="studentSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/students/insert.php">Insert Student Info</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/students/search.php">Search Student</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/students/idcard.php">ID Card</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/students/admitcard.php">Admit Card</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/students/progress.php">Progress Report</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/students/seatplan.php">Generate Seat Plan</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/students/promotion.php">Class Promotion</a></li>
                    </ul>
                </li>
                
                <!-- Teacher Management -->
                <li>
                    <a href="#teacherSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-chalkboard-teacher"></i> Teacher Management
                    </a>
                    <ul class="collapse list-unstyled submenu" id="teacherSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/teachers/insert.php">Insert Teacher Info</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/teachers/search.php">Search Teacher</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/teachers/idcard.php">Teacher ID Card</a></li>
                    </ul>
                </li>
                
                <!-- Staff Management -->
                <li>
                    <a href="#staffSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-users"></i> Staff Management
                    </a>
                    <ul class="collapse list-unstyled submenu" id="staffSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/staff/insert.php">Insert Staff Info</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/staff/search.php">Search Staff</a></li>
                    </ul>
                </li>
                
                <!-- Attendance System -->
                <li>
                    <a href="#attendanceSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-calendar-check"></i> Attendance System
                    </a>
                    <ul class="collapse list-unstyled submenu" id="attendanceSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/attendance/student.php">Student Attendance</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/attendance/teacher.php">Teacher & Staff Attendance</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/attendance/report.php">Attendance Report</a></li>
                    </ul>
                </li>
                
                <!-- Subject Assignment -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/subject/assignment.php">
                        <i class="fas fa-book"></i> Subject Assignment
                    </a>
                </li>
                
                <!-- Class Routine -->
                <li>
                    <a href="#routineSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-clock"></i> Class Routine
                    </a>
                    <ul class="collapse list-unstyled submenu" id="routineSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/routine/create.php">Create Class Routine</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/routine/edit.php">Edit Class Routine</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/routine/view.php">View Class Routine</a></li>
                    </ul>
                </li>
                
                <!-- Marks Distribution -->
                <li>
                    <a href="#marksSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-edit"></i> Marks Distribution
                    </a>
                    <ul class="collapse list-unstyled submenu" id="marksSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/marks/input.php">Input Marks</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/marks/tabulation.php">Tabulation Sheet</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/marks/sms.php">Send Marks By SMS</a></li>
                    </ul>
                </li>
                
                <!-- Result -->
                <li>
                    <a href="#resultSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-chart-bar"></i> Result
                    </a>
                    <ul class="collapse list-unstyled submenu" id="resultSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/result/total.php">Total Result</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/result/annual.php">Annual Result</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/result/merit.php">Merit List</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/result/fail.php">Fail List</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/result/glance.php">Result At A Glance</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/result/publish.php">Publish Result Status</a></li>
                    </ul>
                </li>
                
                <!-- Certificates -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/certificates/testimonial.php">
                        <i class="fas fa-certificate"></i> Testimonial
                    </a>
                </li>
                
                <!-- SMS System -->
                <li>
                    <a href="#smsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-sms"></i> SMS System
                    </a>
                    <ul class="collapse list-unstyled submenu" id="smsSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/sms/create.php">Create SMS</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/sms/teacher.php">SMS to Teacher</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/sms/guardian.php">SMS to Guardian</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/sms/staff.php">SMS to Staff</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/sms/governing.php">SMS to Governing Body</a></li>
                    </ul>
                </li>
                
                <!-- Library -->
                <li>
                    <a href="#librarySubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-book-reader"></i> Library
                    </a>
                    <ul class="collapse list-unstyled submenu" id="librarySubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/library/insert.php">Insert Book</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/library/issue.php">Issue Book</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/library/add.php">Add New Book</a></li>
                    </ul>
                </li>
                
                <!-- Accounts -->
                <li>
                    <a href="#accountsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-money-bill-wave"></i> Accounts
                    </a>
                    <ul class="collapse list-unstyled submenu" id="accountsSubmenu">
                        <li><a href="<?php echo SITE_URL; ?>/accounts/fee_collection.php">Fee Collection</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/fee_heads.php">Create Fee Heads</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/fee_settings.php">Class-wise Fee Settings</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/collection_report.php">Collection Report</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/bank_history.php">Bank History</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/salary_info.php">Employee Salary Info</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/salary_payment.php">Salary Payment</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/accounts/accounting.php">Accounting</a></li>
                    </ul>
                </li>
                
                <!-- Backup -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/backup/index.php">
                        <i class="fas fa-database"></i> Backup
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION['full_name'] ?? 'User'; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile/index.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/settings/index.php"><i class="fas fa-cog"></i> Settings</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content Container -->
            <div class="container-fluid py-4">
                <?php
                $flash = getFlashMessage();
                if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'info'); ?> alert-dismissible fade show">
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php endif; ?>
