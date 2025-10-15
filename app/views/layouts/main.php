<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, maximum-scale=2.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>CT AutoFashion</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/images/icon.png">
    <link rel="apple-touch-icon" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/images/icon.png">
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CSS - Complete Version -->
    <link href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/css/fontawesome-complete.css" rel="stylesheet">
    
    <!-- Main CSS -->
    <link href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/css/main.css" rel="stylesheet">
    
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($_SESSION['user_id']) ? 'logged-in' : 'not-logged-in'; ?>">
    <!-- Include Header -->
    <?php include 'app/views/layouts/header.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Add fade-in animation to main content
        document.addEventListener('DOMContentLoaded', function() {
            var mainContent = document.querySelector('.fade-in');
            if (mainContent) {
                mainContent.classList.add('fade-in');
            }
        });
    </script>
    
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
