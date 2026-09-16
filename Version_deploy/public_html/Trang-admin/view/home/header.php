<!doctype html>
<html class="no-js" lang="en">


<!-- Mirrored from view/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Nov 2023 02:44:52 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ADMIN GALAXY STUDIO</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Content Security Policy - Allow inline scripts for admin panel -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.gstatic.com https://fonts.googleapis.com https://fonts.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';"><?php
    // Cũng có thể set CSP qua PHP header nếu cần
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.gstatic.com;");
    ?>
     <!-- Favicon -->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://www.gstatic.com/charts/loader.js"></script>
<!-- CSS
============================================ -->

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">

<!-- Icon Font CSS -->
<link rel="stylesheet" href="assets/css/vendor/material-design-iconic-font.min.css">
<link rel="stylesheet" href="assets/css/vendor/font-awesome.min.css">
<link rel="stylesheet" href="assets/css/vendor/themify-icons.css">
<link rel="stylesheet" href="assets/css/vendor/cryptocurrency-icons.css">
    <link rel="stylesheet" href="assets/css/ctve.css">

<!-- Plugins CSS -->
<link rel="stylesheet" href="assets/css/plugins/plugins.css">

<!-- Helper CSS -->
<link rel="stylesheet" href="assets/css/helper.css">

<!-- Main Style CSS -->
<link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

<!-- Custom Style CSS Only For Demo Purpose -->
<link id="cus-style" rel="stylesheet" href="assets/css/style-primary.css">
<link rel="stylesheet" href="assets/css/admin-custom.css?v=<?php echo time(); ?>">

<!-- Admin Header Improvements CSS -->
<link rel="stylesheet" href="assets/css/admin-header-improved.css?v=<?php echo time(); ?>">

<!-- INLINE TEST CSS FOR IMMEDIATE EFFECT -->
<style>
    /* IMMEDIATE HEADER IMPROVEMENTS */
    .header-section {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        border-bottom: 3px solid #3498db !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 9999 !important;
        padding: 0px 0 !important;
    }
    
    .header-section .container-fluid {
        max-width: 100% !important;
        padding: 0 30px !important;
    }
    
    .header-section .row {
        width: 100% !important;
        margin: 0 !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }
    
    .header-logo {
        flex: 0 0 auto !important;
    }
    
    .header-right {
        flex: 1 !important;
        display: flex !important;
        justify-content: flex-end !important;
        margin-left: auto !important;
    }
    
    .header-right .row {
        width: auto !important;
        flex: 1 !important;
        justify-content: space-between !important;
    }
    
    .header-right .col-auto:first-child {
        margin-right: auto !important;
    }
    
    .header-right .col-auto:last-child {
        margin-left: auto !important;
    }
    
    /* Đảm bảo toggle button và notification area được dãn ra */
    .header-right .row > .col-auto:first-child {
        flex: 0 0 auto !important;
    }
    
    .header-right .row > .col-auto:last-child {
        flex: 0 0 auto !important;
        margin-left: auto !important;
    }
    
    /* Header notification area alignment */
    .header-notification-area {
        display: flex !important;
        align-items: center !important;
        gap: 20px !important;
        margin-left: auto !important;
    }
    
    /* Responsive spacing */
    @media (max-width: 768px) {
        .header-section .container-fluid {
            padding: 0 15px !important;
        }
        
        .header-right .row {
            gap: 15px !important;
        }
        
        .header-notification-area {
            gap: 10px !important;
        }
    }
    
    /* Add back the gradient animation */
    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #e74c3c, #f39c12, #27ae60, #9b59b6);
        background-size: 400% 100%;
        animation: headerGradient 8s ease infinite;
    }
    
    @keyframes headerGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes headerGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .header-logo h3 {
        background: linear-gradient(135deg, #3498db, #e74c3c) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        margin: 0 !important;
        font-weight: 700 !important;
        font-size: 24px !important;
    }
    
    .side-header-toggle {
        background: linear-gradient(135deg, #3498db, #2980b9) !important;
        border: none !important;
        color: white !important;
        width: 45px !important;
        height: 45px !important;
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3) !important;
    }
    
    .side-header-toggle:hover {
        background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
        transform: translateY(-2px) !important;
    }
    
    .header-notification-area .avatar {
        border: 2px solid #3498db !important;
        border-radius: 50% !important;
        transition: all 0.3s ease !important;
    }
    
    .header-notification-area .avatar:hover {
        border-color: #e74c3c !important;
        transform: scale(1.05) !important;
    }
</style>

</head>

<body>
   
    <div class="main-wrapper">


        <!-- Header Section Start -->
        <div class="header-section">
            <div class="container-fluid">
                <div class="row justify-content-between align-items-center">

                    <!-- Header Logo (Header Left) Start -->
                    <div class="header-logo col-auto">
                        <a href="index.php">
                            <h3><?= __("Quản Trị Galaxy Studio") ?></h3>
                            <!-- <img src="assets/images/logo/logo.png" alt="">
                            <img src="assets/images/logo/logo-light.png" class="logo-light" alt=""> -->
                        </a>
                    </div><!-- Header Logo (Header Left) End -->

                    <!-- Header Right Start -->
                    <div class="header-right flex-grow-1 col-auto">
                        <div class="row justify-content-between align-items-center">

                            <!-- Side Header Toggle & Search Start -->
                            <div class="col-auto">
                                <div class="row align-items-center">

                                    <!--Side Header Toggle-->
                                    <div class="col-auto"><button class="side-header-toggle"><i class="zmdi zmdi-menu"></i></button></div>

                                </div>
                            </div><!-- Side Header Toggle & Search End -->

                            <!-- Header Notifications Area Start -->
                            <div class="col-auto">

                                <ul class="header-notification-area">

                                    <!--Language-->
                                    <li class="adomx-dropdown position-relative col-auto" style="display: flex; align-items: center; font-family: 'Inter', sans-serif;">
                                        <a href="<?= get_lang_url('vi') ?>" style="color: <?= get_current_lang() === 'vi' ? '#ffd564' : '#ffffff' ?>; font-weight: <?= get_current_lang() === 'vi' ? 'bold' : 'normal' ?>; text-decoration: none; font-size: 14px; margin-right: 5px;">VI</a>
                                        <span style="color: rgba(255,255,255,0.3); margin: 0 5px;">|</span>
                                        <a href="<?= get_lang_url('en') ?>" style="color: <?= get_current_lang() === 'en' ? '#ffd564' : '#ffffff' ?>; font-weight: <?= get_current_lang() === 'en' ? 'bold' : 'normal' ?>; text-decoration: none; font-size: 14px; margin-left: 5px;">EN</a>
                                    </li>
                                    <!--User-->
                                    <li class="adomx-dropdown col-auto">
                                        <a class="toggle" href="#">
                                            <span class="user">
                                        <span class="avatar">
                                            <?php
                                            $role_val = isset($_SESSION['user1']['vai_tro']) ? (int)$_SESSION['user1']['vai_tro'] : -1;
                                            $name_val = $_SESSION['user1']['name'] ?? 'User';
                                            
                                            // Determine gradient and initials
                                            switch ($role_val) {
                                                case 2: // ROLE_ADMIN_HE_THONG
                                                    $grad_colors = ['#FF416C', '#FF4B2B'];
                                                    $label_text = 'AD';
                                                    break;
                                                case 4: // ROLE_QUAN_LY_CUM
                                                    $grad_colors = ['#8A2387', '#F27121'];
                                                    $label_text = 'CR';
                                                    break;
                                                case 3: // ROLE_QUAN_LY_RAP
                                                    $grad_colors = ['#4776E6', '#8E54E9'];
                                                    $label_text = 'QL';
                                                    break;
                                                case 1: // ROLE_NHAN_VIEN
                                                    $grad_colors = ['#11998e', '#38ef7d'];
                                                    $label_text = 'NV';
                                                    break;
                                                default:
                                                    $grad_colors = ['#757F9A', '#D7DDE8'];
                                                    $label_text = !empty($name_val) ? mb_strtoupper(mb_substr($name_val, 0, 2, 'UTF-8'), 'UTF-8') : 'U';
                                            }
                                            
                                            $svg_avatar = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="40" height="40" style="border-radius: 50%;">
                                                <defs>
                                                    <linearGradient id="grad-header-' . $role_val . '" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="' . $grad_colors[0] . '" />
                                                        <stop offset="100%" stop-color="' . $grad_colors[1] . '" />
                                                    </linearGradient>
                                                </defs>
                                                <circle cx="50" cy="50" r="50" fill="url(#grad-header-' . $role_val . ')" />
                                                <text x="50" y="55" font-family="\'Inter\', sans-serif" font-size="36" font-weight="800" fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">' . $label_text . '</text>
                                            </svg>';
                                            $avatar_data_uri = 'data:image/svg+xml;base64,' . base64_encode($svg_avatar);
                                            ?>
                                            <img src="<?= $avatar_data_uri ?>" alt="Avatar" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                            <span class="status"></span>
                                            </span>
                                            </span>
                                        </a>

                                        <!-- Dropdown -->
                                        <div class="adomx-dropdown-menu dropdown-menu-user">

                                            <?php
                                            if (isset($_SESSION['user1'])) {
                                                require_once __DIR__ . '/../../helpers/quyen.php';
                                                $user1 = $_SESSION['user1'];
                                                $roleLabel = role_label($user1['vai_tro'] ?? -1);
                                                $name = htmlspecialchars($user1['name'] ?? '', ENT_QUOTES, 'UTF-8');
                                                $email = htmlspecialchars($user1['email'] ?? '', ENT_QUOTES, 'UTF-8');
                                                echo '<div class="head">
                                                      <h5 class="name"><a href="#">' . $roleLabel . ' - ' . $name . ' </a></h5>
                                                      <a class="mail" href="#">' . $email . '</a>
                                                       </div>
                                                         <div class="body">
                                                             <ul>';
                                                echo '<li><a href="index.php?act=dangxuat"><i class="zmdi zmdi-lock-open"></i>' . __("Đăng xuất") . '</a></li>';
                                                echo '</ul></div></div>';
                                            }
                                            ?>



                                    </li>

                                </ul>

                            </div><!-- Header Notifications Area End -->

                        </div>
                    </div><!-- Header Right End -->

                </div>
            </div>
        </div><!-- Header Section End -->

    <!-- Admin Header Enhanced JavaScript -->
    <script src="assets/js/admin-header-enhanced.js?v=<?php echo time(); ?>"></script>
