<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/DanhMucModel.php';

class DanhMucController
{
    private DanhMucModel $danhMucModel;

    public function __construct()
    {
        $this->danhMucModel = new DanhMucModel();
    }

    public function index(): void
    {
        require_permission('danh-muc');

        $keyword = trim((string) ($_GET['q'] ?? ''));

        if ($keyword !== '') {
            $db = db();
            $stmt = $db->prepare("SELECT dm.*, COUNT(hh.ma_hang_hoa) AS so_luong_sp 
                                  FROM danh_muc dm 
                                  LEFT JOIN hang_hoa hh ON hh.ma_danh_muc = dm.ma_danh_muc 
                                  WHERE dm.ma_danh_muc LIKE ? OR dm.ten_danh_muc LIKE ?
                                  GROUP BY dm.ma_danh_muc 
                                  ORDER BY dm.ten_danh_muc");
            $stmt->execute(["%$keyword%", "%$keyword%"]);
            $categories = $stmt->fetchAll();
        } else {
            $categories = $this->danhMucModel->getAllWithProductCount();
        }

        render('danh_muc/index', [
            'pageTitle' => 'Quản lý danh mục',
            'categories' => $categories,
            'q' => $keyword,
        ]);
    }

    public function form(): void
    {
        require_permission('danh-muc');

        $id = trim((string) ($_GET['id'] ?? ''));
        $category = $id !== '' ? $this->danhMucModel->findById($id) : null;

        if ($id !== '' && $category === null) {
            flash('error', 'Không tìm thấy danh mục cần sửa.');
            redirect_to('danh-muc', 'index');
        }

        $this->renderForm($category, [], $category !== null);
    }

    public function save(): void
    {
        require_permission('danh-muc');

        if (!is_post()) {
            redirect_to('danh-muc', 'index');
        }

        $input = [
            'ma_danh_muc' => trim((string) ($_POST['ma_danh_muc'] ?? '')),
            'ten_danh_muc' => trim((string) ($_POST['ten_danh_muc'] ?? '')),
            'mo_ta' => trim((string) ($_POST['mo_ta'] ?? '')),
        ];

        $isEdit = (string) ($_POST['is_edit'] ?? '0') === '1';
        $errors = [];

        if ($input['ma_danh_muc'] === '') {
            $errors[] = 'Mã danh mục không được để trống.';
        }
        if ($input['ten_danh_muc'] === '') {
            $errors[] = 'Tên danh mục không được để trống.';
        }

        if ($errors !== []) {
            $this->renderForm($input, $errors, $isEdit);
            return;
        }

        try {
            if ($isEdit) {
                $this->danhMucModel->update($input['ma_danh_muc'], $input);
                flash('success', 'Cập nhật danh mục thành công.');
            } else {
                if ($this->danhMucModel->findById($input['ma_danh_muc']) !== null) {
                    $errors[] = 'Mã danh mục đã tồn tại.';
                    $this->renderForm($input, $errors, $isEdit);
                    return;
                }
                $this->danhMucModel->create($input);
                flash('success', 'Thêm danh mục thành công.');
            }
            redirect_to('danh-muc', 'index');
        } catch (Throwable $exception) {
            $errors[] = db_error_message($exception);
            $this->renderForm($input, $errors, $isEdit);
        }
    }

    public function delete(): void
    {
        require_permission('danh-muc');
        
        $user = current_user();
        $isAjax = expects_json_response();
        if (($user['ma_chuc_vu'] ?? '') !== 'ADMIN') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Chỉ tài khoản ADMIN mới được phép xoá danh mục.']);
                exit;
            }
            flash('error', 'Chỉ tài khoản ADMIN mới được phép xoá danh mục.');
            redirect_to('danh-muc', 'index');
        }

        if (!is_post()) {
            redirect_to('danh-muc', 'index');
        }

        $id = trim((string) ($_POST['ma_danh_muc'] ?? ''));

        if ($id === '') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy mã danh mục để xóa.']);
                exit;
            }
            flash('error', 'Không tìm thấy mã danh mục để xóa.');
            redirect_to('danh-muc', 'index');
        }

        try {
            $this->danhMucModel->delete($id);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Xóa danh mục thành công.']);
                exit;
            }
            flash('success', 'Xóa danh mục thành công.');
        } catch (Throwable $exception) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => db_error_message($exception)]);
                exit;
            }
            flash('error', db_error_message($exception));
        }

        redirect_to('danh-muc', 'index');
    }

    private function renderForm(?array $category, array $errors, bool $isEdit): void
    {
        render('danh_muc/form', [
            'pageTitle' => $isEdit ? 'Cập nhật danh mục' : 'Thêm danh mục',
            'category' => $category,
            'errors' => $errors,
            'isEdit' => $isEdit,
        ]);
    }
}
