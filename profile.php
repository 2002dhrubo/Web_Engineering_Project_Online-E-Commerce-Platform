<?php 
require_once 'config.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$conn = getConnection();
$user_id = getCurrentUserId();

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_profile') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            if (empty($name) || empty($email)) {
                $error = 'Name and email are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } else {
                // Check if email already exists for another user
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param("si", $email, $user_id);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    $error = 'This email is already registered to another account.';
                } else {
                    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $name, $email, $user_id);
                    
                    if ($stmt->execute()) {
                        $_SESSION['name'] = $name;
                        $_SESSION['email'] = $email;
                        $user['name'] = $name;
                        $user['email'] = $email;
                        $success = 'Profile updated successfully!';
                    } else {
                        $error = 'Failed to update profile. Please try again.';
                    }
                }
            }
        } elseif ($action === 'change_password') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error = 'All password fields are required.';
            } elseif (strlen($new_password) < 6) {
                $error = 'New password must be at least 6 characters long.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'New passwords do not match.';
            } elseif (!password_verify($current_password, $user['password'])) {
                $error = 'Current password is incorrect.';
            } else {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $user_id);
                
                if ($stmt->execute()) {
                    $success = 'Password changed successfully!';
                } else {
                    $error = 'Failed to change password. Please try again.';
                }
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .profile-section {
            padding: 120px 20px 60px;
            max-width: 800px;
            margin: 0 auto;
        }
        .page-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
        }
        .page-title i {
            color: #667eea;
            margin-right: 10px;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
        }
        .card-header h2 {
            font-size: 18px;
            margin: 0;
        }
        .card-header h2 i {
            margin-right: 10px;
        }
        .card-body {
            padding: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-group label i {
            color: #667eea;
            margin-right: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }
        .form-group input:disabled {
            background: #f5f5f5;
            color: #888;
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }
        .user-details h3 {
            margin: 0 0 5px;
            color: #333;
        }
        .user-details p {
            margin: 0;
            color: #888;
            font-size: 14px;
        }
        .user-details .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
        .role-user {
            background: #e3f2fd;
            color: #1976d2;
        }
        .role-admin {
            background: #fce4ec;
            color: #c2185b;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="profile-section">
        <h1 class="page-title"><i class="fas fa-user-circle"></i> My Profile</h1>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo escape($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo escape($success); ?>
        </div>
        <?php endif; ?>
        
        <!-- User Overview -->
        <div class="profile-card">
            <div class="card-body">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo escape($user['name']); ?></h3>
                        <p><?php echo escape($user['email']); ?></p>
                        <span class="role-badge role-<?php echo $user['role']; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                </div>
                <p style="color: #888; font-size: 14px;">
                    <i class="fas fa-calendar"></i> Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                </p>
            </div>
        </div>
        
        <!-- Update Profile Form -->
        <div class="profile-card">
            <div class="card-header">
                <h2><i class="fas fa-edit"></i> Update Profile</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo escape($user['name']); ?>"
                                   placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo escape($user['email']); ?>"
                                   placeholder="Enter your email">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Change Password Form -->
        <div class="profile-card">
            <div class="card-header">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="form-group">
                        <label for="current_password"><i class="fas fa-key"></i> Current Password</label>
                        <input type="password" id="current_password" name="current_password" required
                               placeholder="Enter your current password">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password"><i class="fas fa-lock"></i> New Password</label>
                            <input type="password" id="new_password" name="new_password" required
                                   placeholder="Enter new password (min 6 characters)"
                                   minlength="6">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-lock"></i> Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   placeholder="Confirm new password"
                                   minlength="6">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-save">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
