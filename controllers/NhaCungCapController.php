<?php
require_once BASE_PATH . 'models/NhaCungCapModel.php';

class NhaCungCapController {
    private $nhaCungCapModel;
    
    public function __construct() {
        $this->nhaCungCapModel = new NhaCungCapModel();
    }
    
    public function index() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY', 'THU_KHO']);
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $keyword = $_GET['keyword'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';
        
        if ($keyword || $trang_thai) {
            $nhaCungCaps = $this->nhaCungCapModel->search($keyword, $limit, $offset);
            $total = $this->nhaCungCapModel->countSearch($keyword);
        } else {
            $nhaCungCaps = $this->nhaCungCapModel->getAllWithProductCount($limit, $offset);
            $total = $this->nhaCungCapModel->countAll();
        }
        
        $totalPages = ceil($total / $limit);
        $activeCount = $this->nhaCungCapModel->countActive();
        $totalCount = $total;
        
        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/nha_cung_cap/index.php';
        include BASE_PATH . 'views/layout/footer.php';
    }
    
    public function create() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_nha_cung_cap' => $_POST['ma_nha_cung_cap'],
                'ten_nha_cung_cap' => $_POST['ten_nha_cung_cap'],
                'dia_chi' => $_POST['dia_chi'] ?? null,
                'email' => $_POST['email'] ?? null,
                'so_dien_thoai' => $_POST['so_dien_thoai'] ?? null,
                'ten_nguoi_lien_he' => $_POST['ten_nguoi_lien_he'] ?? null,
                'trang_thai' => $_POST['trang_thai'] ?? 'HOAT_DONG'
            ];
            
            if ($this->nhaCungCapModel->create($data)) {
                $_SESSION['success'] = 'Thêm nhà cung cấp thành công!';
                header('Location: ' . BASE_URL . 'index.php?controller=nha_cung_cap&action=index');
                exit();
            } else {
                $error = 'Thêm nhà cung cấp thất bại!';
            }
        }
        
        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/nha_cung_cap/form.php';
        include BASE_PATH . 'views/layout/footer.php';
    }
    
    public function edit() {
        checkLogin();
        checkRole(['ADMIN', 'QUAN_LY']);
        
        $ma_nha_cung_cap = $_GET['id'] ?? '';
        $nhaCungCap = $this->nhaCungCapModel->findById($ma_nha_cung_cap);
        
        if (!$nhaCungCap) {
            $_SESSION['error'] = 'Không tìm thấy nhà cung cấp!';
            header('Location: ' . BASE_URL . 'index.php?controller=nha_cung_cap&action=index');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ten_nha_cung_cap' => $_POST['ten_nha_cung_cap'],
                'dia_chi' => $_POST['dia_chi'] ?? null,
                'email' => $_POST['email'] ?? null,
                'so_dien_thoai' => $_POST['so_dien_thoai'] ?? null,
                'ten_nguoi_lien_he' => $_POST['ten_nguoi_lien_he'] ?? null,
                'trang_thai' => $_POST['trang_thai'] ?? 'HOAT_DONG'
            ];
            
            if ($this->nhaCungCapModel->update($ma_nha_cung_cap, $data)) {
                $_SESSION['success'] = 'Cập nhật nhà cung cấp thành công!';
                header('Location: ' . BASE_URL . 'index.php?controller=nha_cung_cap&action=index');
                exit();
            } else {
                $error = 'Cập nhật nhà cung cấp thất bại!';
            }
        }
        
        include BASE_PATH . 'views/layout/header.php';
        include BASE_PATH . 'views/nha_cung_cap/form.php';
        include BASE_PATH . 'views/layout/footer.php';
    }
    
    public function delete() {
        checkLogin();
        checkRole(['ADMIN']);
        
        $ma_nha_cung_cap = $_GET['id'] ?? '';
        
        if ($this->nhaCungCapModel->delete($ma_nha_cung_cap)) {
            $_SESSION['success'] = 'Xóa nhà cung cấp thành công!';
        } else {
            $_SESSION['error'] = 'Xóa nhà cung cấp thất bại!';
        }
        
        header('Location: ' . BASE_URL . 'index.php?controller=nha_cung_cap&action=index');
        exit();
    }
}