<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . ' | Parking System' : 'Parking System'; ?></title>
<link rel="stylesheet" href="<?php echo isset($asset_path) ? $asset_path : ''; ?>assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-brand">🅿️ Online Car Parking System</div>
    <nav>
        <?php if (isset($_SESSION['admin_id'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> (Admin)</span>
            <a href="<?php echo isset($asset_path) ? $asset_path : ''; ?>admin/logout.php">Logout</a>
        <?php elseif (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="<?php echo isset($asset_path) ? $asset_path : ''; ?>user/logout.php">Logout</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
