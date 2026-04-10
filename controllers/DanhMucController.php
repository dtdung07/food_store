<?php
require_once BASE_PATH . 'models/DanhMucModel.php';

class DanhMucController {
    private $danhMucModel;
    
    public function __construct() {
        $this->danhMucModel = new DanhMucModel();
    }
    
    public function index() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);
        
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $danhMucs = $this->danhMucModel->getAll($limit, $offset);
        $total = $this->danhMucModel->countAll();
        $totalPages = ceil($total / $limit);
        
        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/danh_muc/index.php';
        include BASE_PATH . 'views/layout/footer.php';
    }
    
    public function create() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_danh_muc' => $_POST['ma_danh_muc'],
                'ten_danh_muc' => $_POST['ten_danh_muc'],
                'mo_ta' => $_POST['mo_ta'] ?? ''
            ];
            
            if ($this->danhMucModel->create($data)) {
                $_SESSION['success'] = 'Thêm danh mục thành công!';
                header('Location: ' . BASE_URL . 'index.php?controller=danh_muc&action=index');
                exit();
            } else {
                $error = 'Thêm danh mục thất bại!';
            }
        }
        
        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/danh_muc/form.php';
        include BASE_PATH . 'views/layout/footer.php';
    }
    
    public function edit() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);
        
        $ma_danh_muc = $_GET['id'] ?? '';
        $danhMuc = $this->danhMucModel->findById($ma_danh_muc);
        
        if (!$danhMuc) {
            $_SESSION['error'] = 'Không tìm thấy danh mục!';
            header('Location: ' . BASE_URL . 'index.php?controller=danh_muc&action=index');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ten_danh_muc' => $_POST['ten_danh_muc'],
                'mo_ta' => $_POST['mo_ta'] ?? ''
            ];
            
            if ($this->danhMucModel->update($ma_danh_muc, $data)) {
                $_SESSION['success'] = 'Cập nhật danh mục thành công!';
                header('Location: ' . BASE_URL . 'index.php?controller=danh_muc&action=index');
                exit();
            } else {
                $error = 'Cập nhật danh mục thất bại!';
            }
        }
        
        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/danh_muc/form.php';
        include BASE_PATH . 'views/layout/footer.php';
    }
    
    public function delete() {
        checkLogin();
        checkRole(['ADMIN']);
        
        $ma_danh_muc = $_GET['id'] ?? '';
        
        if ($this->danhMucModel->delete($ma_danh_muc)) {
            $_SESSION['success'] = 'Xóa danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Xóa danh mục thất bại!';
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=danh_muc&action=index');
        exit();
    }
}