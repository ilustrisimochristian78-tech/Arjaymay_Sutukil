<?php
// ======================================================
// MAIN INDEX - Enhanced Role Selection Page
// ======================================================
require_once 'config/database.php';

// If already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: modules/admin/dashboard.php');
    } elseif (isCashier()) {
        header('Location: modules/cashier/dashboard.php');
    }
    exit;
}

// Get site settings
$settings = getSiteSettings();
$siteLogo = getSiteLogo();
$siteName = $settings['site_name'] ?? 'Arjaymay Sutukil';
$favicon = getSiteFavicon();
$backgroundImage = getBackgroundImage();

// Show role selection page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?> - Role Selection</title>
    <?php if ($favicon): ?>
    <link rel="icon" href="<?php echo $favicon; ?>">
    <?php endif; ?>
    <?php include __DIR__ . '/includes/asset_links.php'; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #1a3d1b, #2c5f2d);
            background-size: cover;
            background-position: center;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 0;
        }
        <?php if ($backgroundImage): ?>
        body {
            background-image: url('<?php echo $backgroundImage; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        <?php endif; ?>
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            padding: 40px 36px;
            position: relative;
            z-index: 1;
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header .logo-icon {
            margin-bottom: 12px;
        }
        .login-header .logo-icon img {
            max-height: 80px;
            max-width: 200px;
            object-fit: contain;
        }
        .login-header .logo-icon i {
            font-size: 3rem;
            color: #2c5f2d;
        }
        .login-header h1 {
            font-size: 1.5rem;
            color: #2c5f2d;
            margin-bottom: 4px;
        }
        .login-header p {
            color: #636e72;
            font-size: 0.9rem;
        }
        .role-selector h3 {
            text-align: center;
            margin-bottom: 16px;
            color: #2d3436;
        }
        .role-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .role-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            color: #2d3436;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            background: rgba(255,255,255,0.8);
        }
        .role-btn i {
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .role-btn span {
            font-weight: 600;
            font-size: 1rem;
        }
        .role-btn small {
            color: #636e72;
            font-size: 0.8rem;
        }
        .role-btn.admin-btn { border-color: #e74c3c; }
        .role-btn.admin-btn i { color: #e74c3c; }
        .role-btn.admin-btn:hover { background: #fef0ef; border-color: #c0392b; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(231,76,60,0.2); }
        .role-btn.cashier-btn { border-color: #2c5f2d; }
        .role-btn.cashier-btn i { color: #2c5f2d; }
        .role-btn.cashier-btn:hover { background: #f0f7f0; border-color: #1a3d1b; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(44,95,45,0.2); }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #636e72;
        }
        .login-footer .brand {
            color: #2c5f2d;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-icon">
                <?php if ($siteLogo): ?>
                    <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>">
                <?php else: ?>
                    <i class="fas fa-utensils"></i>
                <?php endif; ?>
            </div>
            <h1><?php echo $siteName; ?></h1>
            <p>Walk-in Ordering & Automated Sales Recording System</p>
        </div>
        
        <div class="role-selector">
            <h3>Select Your Role</h3>
            <div class="role-buttons">
                <a href="modules/auth/admin_login.php" class="role-btn admin-btn">
                    <i class="fas fa-user-shield"></i>
                    <span>Administrator</span>
                    <small>Manage system, menu, users & reports</small>
                </a>
                <a href="modules/auth/cashier_login.php" class="role-btn cashier-btn">
                    <i class="fas fa-user-tie"></i>
                    <span>Cashier</span>
                    <small>Process orders & payments</small>
                </a>
            </div>
        </div>
        
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> <span class="brand"><?php echo $siteName; ?></span>. All rights reserved.</p>
            <p style="font-size: 0.7rem; color: #b2b2b2; margin-top: 4px;">Powered by Arjaymay POS System v2.0</p>
        </div>
    </div>
</body>
</html>