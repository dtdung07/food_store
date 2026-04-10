<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodStore - Hệ thống quản lý siêu thị thực phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="sidebar-header">
                    <h4><i class="fas fa-store"></i> FoodStore</h4>
                    <p>Quản lý siêu thị thực phẩm</p>
                </div>
                
                <div class="user-info">
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user']['ten_nhan_vien'] ?? '') ?>
                    </div>
                    <div class="user-role">
                        <?php
                        $roleName = '';
                        switch($_SESSION['user']['ma_chuc_vu'] ?? '') {
                            case 'ADMIN': $roleName = 'Quản trị viên'; break;
                            case 'QUAN_LY': $roleName = 'Quản lý'; break;
                            case 'THU_KHO': $roleName = 'Thủ kho'; break;
                            case 'THU_NGAN': $roleName = 'Thu ngân'; break;
                            default: $roleName = 'Nhân viên';
                        }
                        echo $roleName;
                        ?>
                    </div>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link <?= ($_GET['controller'] ?? '') == 'dashboard' ? 'active' : '' ?>" href="index.php?controller=dashboard&action=index">
                        <i class="fas fa-chart-line"></i> <span>Dashboard</span>
                    </a>
                    
                    <?php if (in_array($_SESSION['user']['ma_chuc_vu'] ?? '', ['ADMIN', 'QUAN_LY'])): ?>
                        <a class="nav-link <?= ($_GET['controller'] ?? '') == 'danh_muc' ? 'active' : '' ?>" href="index.php?controller=danh_muc&action=index">
                            <i class="fas fa-folder-tree"></i> <span>Danh mục</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['user']['ma_chuc_vu'] ?? '', ['ADMIN', 'QUAN_LY', 'THU_KHO'])): ?>
                        <a class="nav-link <?= ($_GET['controller'] ?? '') == 'hang_hoa' ? 'active' : '' ?>" href="index.php?controller=hang_hoa&action=index">
                            <i class="fas fa-boxes"></i> <span>Hàng hóa</span>
                        </a>
                        
                        <a class="nav-link <?= ($_GET['controller'] ?? '') == 'nha_cung_cap' ? 'active' : '' ?>" href="index.php?controller=nha_cung_cap&action=index">
                            <i class="fas fa-truck"></i> <span>Nhà cung cấp</span>
                        </a>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <a class="nav-link" href="index.php?controller=auth&action=logout">
                        <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 content-wrapper">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <div class="welcome-text">
                            <!-- <i class="fas fa-home"></i> <?= ucfirst($_GET['controller'] ?? 'dashboard') ?> -->
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="date-info me-3">
                                <i class="far fa-calendar-alt"></i> <?= date('d/m/Y') ?>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                    <div class="user-avatar">
                                        <i class="fas fa-user-circle fa-2x" style="color: var(--primary-color);"></i>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Hồ sơ</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Cài đặt</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <div class="main-content">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>