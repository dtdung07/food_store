<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/PhieuHuyModel.php';
require_once APP_ROOT . '/models/HangHoaModel.php';
require_once APP_ROOT . '/models/LoHangModel.php';


//Controller quản lý Phiếu hủy hàng, Xử lý lập phiếu, xem danh sách, duyệt và từ chối phiếu hủy
class PhieuHuyController
{
    private PhieuHuyModel $phieuHuyModel;
    private HangHoaModel $hangHoaModel;
    private LoHangModel $loHangModel;

    public function __construct()
    {
        $this->phieuHuyModel = new PhieuHuyModel();
        $this->hangHoaModel = new HangHoaModel();
        $this->loHangModel = new LoHangModel();
    }

    //Danh sách phiếu hủy
    public function index(): void
    {
        require_permission('phieu-huy');

        $filters = [
            'q'         => trim((string) ($_GET['q'] ?? '')),
            'status'    => trim((string) ($_GET['status'] ?? 'all')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'   => trim((string) ($_GET['date_to'] ?? '')),
        ];

        $slips = $this->phieuHuyModel->all(
            $filters['q'],
            $filters['status'],
            $filters['date_from'],
            $filters['date_to']
        );

        //Thống kê
        $allSlips = $this->phieuHuyModel->all();
        $stats = [
            'total'      => count($allSlips),
            'cho_duyet'  => count(array_filter($allSlips, fn($s) => $s['trang_thai'] === 'CHO_DUYET')),
            'da_duyet'   => count(array_filter($allSlips, fn($s) => $s['trang_thai'] === 'DA_DUYET')),
            'tu_choi'    => count(array_filter($allSlips, fn($s) => $s['trang_thai'] === 'TU_CHOI')),
        ];

        render('phieu_huy/index', [
            'pageTitle' => 'Phiếu hủy hàng',
            'slips'     => $slips,
            'filters'   => $filters,
            'stats'     => $stats,
        ]);
    }

    //Form lập phiếu hủy mới
    public function form(): void
    {
        require_permission('phieu-huy');

        $nextId = $this->phieuHuyModel->generateId();

        render('phieu_huy/form', [
            'pageTitle' => 'Lập phiếu hủy hàng',
            'nextId'    => $nextId,
            'errors'    => [],
        ]);
    }

    //Xử lý lưu phiếu hủy (trạng thái Chờ duyệt)
    public function save(): void
    {
        require_permission('phieu-huy');

        if (!is_post()) {
            redirect_to('phieu-huy', 'index');
        }

        $header = [
            'ly_do_huy_chung' => trim((string) ($_POST['ly_do_huy_chung'] ?? '')),
            'ma_nhan_vien'    => current_user()['ma_nhan_vien'] ?? '',
        ];

        $details = [];
        $maHangHoaArr = $_POST['ma_hang_hoa'] ?? [];
        $soLuongArr = $_POST['so_luong'] ?? [];
        $lyDoSelectArr = $_POST['ly_do_detail_select'] ?? [];
        $lyDoCustomArr = $_POST['ly_do_detail_custom'] ?? [];

        //Lấy từng dòng chi tiết phiếu hủy
        for ($i = 0, $count = count($maHangHoaArr); $i < $count; $i++) {
            $mhh = trim((string) ($maHangHoaArr[$i] ?? ''));
            if ($mhh === '') {
                continue;
            }

            $selectVal = trim((string) ($lyDoSelectArr[$i] ?? 'HET_HAN'));
            $lyDoHuy = $selectVal;
            if ($selectVal === 'KHAC') {
                $customVal = trim((string) ($lyDoCustomArr[$i] ?? ''));
                $lyDoHuy = ($customVal !== '') ? $customVal : 'KHAC';
            }

            $details[] = [
                'ma_hang_hoa' => $mhh,
                'so_luong'    => (int) ($soLuongArr[$i] ?? 0),
                'ly_do_huy'   => $lyDoHuy,
            ];
        }

        //Validate
        $errors = $this->validatePhieuHuy($details);

        if ($errors !== []) {
            render('phieu_huy/form', [
                'pageTitle' => 'Lập phiếu hủy hàng',
                'nextId'    => $this->phieuHuyModel->generateId(),
                'errors'    => $errors,
                'old'       => $_POST,
            ]);
            return;
        }

        try {
           //Tạo phiếu hủy mới với trạng thái CHỜ DUYỆT
            $maPhieu = $this->phieuHuyModel->create($header, $details);
            flash('success', "Lập phiếu hủy thành công! Mã phiếu: {$maPhieu}. Phiếu đang chờ Quản lý duyệt.");
            redirect_to('phieu-huy', 'detail', ['id' => $maPhieu]);
        } catch (Throwable $e) {
            flash('error', 'Lỗi lập phiếu hủy: ' . $e->getMessage());
            render('phieu_huy/form', [
                'pageTitle' => 'Lập phiếu hủy hàng',
                'nextId'    => $this->phieuHuyModel->generateId(),
                'errors'    => [$e->getMessage()],
                'old'       => $_POST,
            ]);
        }
    }

    //Chi tiết phiếu hủy và Giao diện duyệt/từ chối
    public function detail(): void
    {
        require_permission('phieu-huy');

        //Lấy mã phiếu hủy
        $id = trim((string) ($_GET['id'] ?? ''));
        $slip = $id !== '' ? $this->phieuHuyModel->find($id) : null;

        if ($slip === null) {
            flash('error', 'Không tìm thấy phiếu hủy.');
            redirect_to('phieu-huy', 'index');
        }

        //Kiểm tra quyền
        $currentRole = current_user()['ma_chuc_vu'] ?? '';
        $canApprove = in_array($currentRole, ['ADMIN', 'QUAN_LY'], true)
                      && $slip['trang_thai'] === 'CHO_DUYET';

        render('phieu_huy/detail', [
            'pageTitle'  => 'Chi tiết phiếu hủy: ' . $id,
            'slip'       => $slip,
            'canApprove' => $canApprove,
        ]);
    }

    //Duyệt phiếu hủy -> Trừ kho
    public function approve(): void
    {
        require_permission('phieu-huy');

        //Kiểm tra quyền duyệt
        $currentRole = current_user()['ma_chuc_vu'] ?? '';
        if (!in_array($currentRole, ['ADMIN', 'QUAN_LY'], true)) {
            flash('error', 'Bạn không có quyền duyệt phiếu hủy.');
            redirect_to('phieu-huy', 'index');
        }

        //Kiểm tra phương thức gửi
        if (!is_post()) {
            redirect_to('phieu-huy', 'index');
        }

        $maPhieu = trim((string) ($_POST['ma_phieu_huy'] ?? ''));
        $maNguoiDuyet = current_user()['ma_nhan_vien'] ?? '';

        try {
            $this->phieuHuyModel->approve($maPhieu, $maNguoiDuyet);
            flash('success', 'Duyệt phiếu hủy thành công. Đã cập nhật tồn kho.');
        } catch (Throwable $e) {
            flash('error', 'Lỗi duyệt phiếu: ' . $e->getMessage());
        }

        redirect_to('phieu-huy', 'detail', ['id' => $maPhieu]);
    }

    //Từ chối phiếu hủy
    public function reject(): void
    {
        require_permission('phieu-huy');

        //Kiểm tra quyền từ chối
        $currentRole = current_user()['ma_chuc_vu'] ?? '';
        if (!in_array($currentRole, ['ADMIN', 'QUAN_LY'], true)) {
            flash('error', 'Bạn không có quyền từ chối phiếu hủy.');
            redirect_to('phieu-huy', 'index');
        }

        //Kiểm tra phương thức gửi
        if (!is_post()) {
            redirect_to('phieu-huy', 'index');
        }

        //Lấy mã phiếu hủy
        $maPhieu = trim((string) ($_POST['ma_phieu_huy'] ?? ''));
        $lyDo = trim((string) ($_POST['ly_do_tu_choi'] ?? ''));
        $maNguoiDuyet = current_user()['ma_nhan_vien'] ?? '';

        //Kiểm tra lý do từ chối
        if ($lyDo === '') {
            flash('error', 'Vui lòng nhập lý do từ chối.');
            redirect_to('phieu-huy', 'detail', ['id' => $maPhieu]);
        }

        try {
            //Từ chối phiếu hủy
            $this->phieuHuyModel->reject($maPhieu, $maNguoiDuyet, $lyDo);
            flash('success', 'Đã từ chối phiếu hủy.');
        } catch (Throwable $e) {
            flash('error', 'Lỗi từ chối phiếu: ' . $e->getMessage());
        }

        redirect_to('phieu-huy', 'detail', ['id' => $maPhieu]);
    }

    //AJAX: Lấy danh sách lô hàng của sản phẩm
    public function get_lo_hang(): void
    {
        require_login();

        $maHangHoa = trim((string) ($_GET['ma_hang_hoa'] ?? ''));
        $lots = $maHangHoa !== '' ? $this->hangHoaModel->getLoHang($maHangHoa) : [];

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $lots]);
        exit;
    }

    //-----------VALIDATION----------

    //Validate phiếu hủy
    private function validatePhieuHuy(array $details): array
    {
        $errors = [];

        //Kiểm tra số lượng mặt hàng
        if (empty($details)) {
            $errors[] = 'Phiếu hủy phải có ít nhất 1 mặt hàng.';
        }

        //Kiểm tra số lượng từng mặt hàng
        foreach ($details as $i => $d) {
            $row = $i + 1;

            if ($d['so_luong'] <= 0) {
                $errors[] = "Dòng {$row}: Số lượng hủy phải lớn hơn 0.";
            }

            if ($d['ly_do_huy'] === '') {
                $errors[] = "Dòng {$row}: Bắt buộc phải chọn lý do hủy.";
            } elseif ($d['ly_do_huy'] === 'KHAC') {
                $errors[] = "Dòng {$row}: Bắt buộc phải nhập chi tiết lý do hủy khi chọn lý do 'Khác'.";
            }
        }

        return $errors;
    }
}
