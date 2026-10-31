<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect(SITE_URL . '/dashboard/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND role = ? AND status = 'active'");
        $stmt->execute([$username, $role]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Update last login
            $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            redirect(SITE_URL . '/dashboard/index.php');
        } else {
            $error = 'Invalid username or password';
        }
    }
}

// Get settings for display
$db = getDB();
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo $settings['school_name'] ?? 'School Management System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, rgba(67,67,156,.96), rgba(98,67,156,.92)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1800&q=85') center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-top: 5px solid #f4b942;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 60px;
            color: #43439c;
            margin-bottom: 15px;
        }
        .login-header h2 {
            color: #333;
            font-weight: 600;
        }
        .login-header p {
            color: #666;
        }
        .form-floating {
            margin-bottom: 15px;
        }
        .btn-login {
            background: linear-gradient(135deg, #43439c 0%, #62439c 100%);
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 67, 156, 0.4);
        }
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .role-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }
        .role-btn.active {
            border-color: #43439c;
            background: #f0f4ff;
            color: #43439c;
        }
        .role-btn i {
            display: block;
            font-size: 24px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-school"></i>
            <h2><?php echo $settings['school_name'] ?? 'School Management System'; ?></h2>
            <p>Please sign in to continue</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="role" id="role" value="admin">
            
            <div class="role-selector">
                <div class="role-btn active" onclick="selectRole('admin', this)">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </div>
                <div class="role-btn" onclick="selectRole('teacher', this)">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teacher</span>
                </div>
                <div class="role-btn" onclick="selectRole('staff', this)">
                    <i class="fas fa-users"></i>
                    <span>Staff</span>
                </div>
            </div>
            
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username"><i class="fas fa-user me-2"></i>Username</label>
            </div>
            
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">Default: admin / bd123</small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectRole(role, element) {
            document.getElementById('role').value = role;
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
        }
    </script>
</body>
</html>
