<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/PhieuNhapModel.php';
require_once APP_ROOT . '/models/PhieuXuatModel.php';
require_once APP_ROOT . '/models/HangHoaModel.php';
require_once APP_ROOT . '/models/NhaCungCapModel.php';
require_once APP_ROOT . '/models/LoHangModel.php';

//Controller quản lý kho hàng, Xử lý cả Nhập kho và Xuất kho
class KhoController
{
    private PhieuNhapModel $phieuNhapModel;
    private PhieuXuatModel $phieuXuatModel;
    private HangHoaModel $hangHoaModel;
    private NhaCungCapModel $nhaCungCapModel;
    private LoHangModel $loHangModel;

    public function __construct()
    {
        $this->phieuNhapModel = new PhieuNhapModel();
        $this->phieuXuatModel = new phieuXuatModel();
        $this->hangHoaModel = new HangHoaModel();
        $this->nhaCungCapModel = new NhaCungCapModel();
        $this->loHangModel = new LoHangModel();
    }

    //-------NHẬP KHO-----

    //Danh sách phiếu nhập kho
    public function nhap_index(): void
    {
        require_permission('kho'); //kiểm tra quyền

        //lấy điều kiện tìm kiếm từ URL
        $filters = [
            'q'         => trim((string) ($_GET['q'] ?? '')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'   => trim((string) ($_GET['date_to'] ?? '')),
        ];

        //Danh sách phiếu nhập
        $receipts = $this->phieuNhapModel->all($filters['q'], $filters['date_from'], $filters['date_to']);

        render('kho/nhap_index', [
            'pageTitle' => 'Nhập kho',
            'receipts'  => $receipts,
            'filters'   => $filters,
            'stats'     => [
                'total_count'  => $this->phieuNhapModel->count(),
                'total_amount' => $this->phieuNhapModel->totalAmount(),
            ],
        ]);
    }

    //Form lập phiếu nhập kho
    public function nhap_form(): void
    {
        require_permission('kho');

        render('kho/nhap_form', [
            'pageTitle'  => 'Lập phiếu nhập kho',
            'suppliers'  => $this->nhaCungCapModel->allActive(),
            'errors'     => [],
        ]);
    }

    //Xử lý lưu phiếu nhập kho
    public function nhap_save(): void
    {
        require_permission('kho');

        if (!is_post()) {
            redirect_to('kho', 'nhap_index');
        }

        $header = [
            'ma_nha_cung_cap' => trim((string) ($_POST['ma_nha_cung_cap'] ?? '')),
            'ghi_chu'         => trim((string) ($_POST['ghi_chu'] ?? '')),
            'ma_nhan_vien'    => current_user()['ma_nhan_vien'] ?? '',
        ];

        //Lấy từng dòng chi tiết phiếu nhập
        $details = [];
        $maHangHoaArr = $_POST['ma_hang_hoa'] ?? [];
        $soLuongArr = $_POST['so_luong'] ?? [];
        $donGiaArr = $_POST['don_gia_nhap'] ?? [];
        $maLoArr = $_POST['ma_lo_hang'] ?? [];
        $ngaySXArr = $_POST['ngay_san_xuat'] ?? [];
        $hsdArr = $_POST['han_su_dung'] ?? [];

        for ($i = 0, $count = count($maHangHoaArr); $i < $count; $i++) {
            $mhh = trim((string) ($maHangHoaArr[$i] ?? ''));
            if ($mhh === '') {
                continue;
            }

            $details[] = [
                'ma_hang_hoa'   => $mhh,
                'so_luong'      => (float) ($soLuongArr[$i] ?? 0.0),
                'don_gia_nhap'  => (float) ($donGiaArr[$i] ?? 0),
                'ma_lo_hang'    => trim((string) ($maLoArr[$i] ?? '')),
                'ngay_san_xuat' => trim((string) ($ngaySXArr[$i] ?? '')),
                'han_su_dung'   => trim((string) ($hsdArr[$i] ?? '')),
            ];
        }

        //Validate
        $errors = $this->validateNhap($header, $details);

        if ($errors !== []) {
            render('kho/nhap_form', [
                'pageTitle' => 'Lập phiếu nhập kho',
                'suppliers' => $this->nhaCungCapModel->allActive(),
                'errors'    => $errors,
                'old'       => $_POST,
            ]);
            return;
        }

        try {
            $maPhieu = $this->phieuNhapModel->create($header, $details);
            flash('success', "Nhập kho thành công! Mã phiếu: {$maPhieu}");
            redirect_to('kho', 'nhap_detail', ['id' => $maPhieu]);
        } catch (Throwable $e) {
            flash('error', 'Lỗi nhập kho: ' . $e->getMessage());
            render('kho/nhap_form', [
                'pageTitle' => 'Lập phiếu nhập kho',
                'suppliers' => $this->nhaCungCapModel->allActive(),
                'errors'    => [$e->getMessage()],
                'old'       => $_POST,
            ]);
        }
    }

    //Chi tiết phiếu nhập
    public function nhap_detail(): void
    {
        require_permission('kho');

        $id = trim((string) ($_GET['id'] ?? ''));
        $receipt = $id !== '' ? $this->phieuNhapModel->find($id) : null;

        if ($receipt === null) {
            flash('error', 'Không tìm thấy phiếu nhập.');
            redirect_to('kho', 'nhap_index');
        }

        render('kho/nhap_detail', [
            'pageTitle' => 'Chi tiết phiếu nhập: ' . $id,
            'receipt'   => $receipt,
        ]);
    }

    //------Xuất kho ra quầy-----

    //Danh sách phiếu xuất kho ra quầy
    public function xuat_index(): void
    {
        require_permission('kho');

        $filters = [
            'q'         => trim((string) ($_GET['q'] ?? '')),
            'date_from' => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'   => trim((string) ($_GET['date_to'] ?? '')),
        ];

        $exports = $this->phieuXuatModel->all($filters['q'], $filters['date_from'], $filters['date_to']);

        render('kho/xuat_index', [
            'pageTitle' => 'Xuất kho ra quầy',
            'exports'   => $exports,
            'filters'   => $filters,
            'stats'     => [
                'total_count' => $this->phieuXuatModel->count(),
            ],
        ]);
    }

    //Form lập phiếu xuất kho
    public function xuat_form(): void
    {
        require_permission('kho');

        $nextId = $this->phieuXuatModel->generateId();

        render('kho/xuat_form', [
            'pageTitle' => 'Lập phiếu xuất kho ra quầy',
            'nextId'    => $nextId,
            'errors'    => [],
        ]);
    }

    //Xử lý lưu phiếu xuất kho
    public function xuat_save(): void
    {
        require_permission('kho');

        if (!is_post()) {
            redirect_to('kho', 'xuat_index');
        }

        $header = [
            'ghi_chu'      => trim((string) ($_POST['ghi_chu'] ?? '')),
            'ma_nhan_vien' => current_user()['ma_nhan_vien'] ?? '',
        ];

        $details = [];
        $maHangHoaArr = $_POST['ma_hang_hoa'] ?? [];
        $soLuongArr = $_POST['so_luong'] ?? [];

        for ($i = 0, $count = count($maHangHoaArr); $i < $count; $i++) {
            $mhh = trim((string) ($maHangHoaArr[$i] ?? ''));
            if ($mhh === '') {
                continue;
            }

            $details[] = [
                'ma_hang_hoa' => $mhh,
                'so_luong'    => (float) ($soLuongArr[$i] ?? 0.0),
            ];
        }

        $errors = $this->validateXuat($details);

        if ($errors !== []) {
            $nextId = $this->phieuXuatModel->generateId();
            render('kho/xuat_form', [
                'pageTitle' => 'Lập phiếu xuất kho ra quầy',
                'nextId'    => $nextId,
                'errors'    => $errors,
                'old'       => $_POST,
            ]);
            return;
        }

        try {
            $maPhieu = $this->phieuXuatModel->create($header, $details);
            flash('success', "Xuất kho thành công! Mã phiếu: {$maPhieu}");
            redirect_to('kho', 'xuat_detail', ['id' => $maPhieu]);
        } catch (Throwable $e) {
            flash('error', 'Lỗi xuất kho: ' . $e->getMessage());
            $nextId = $this->phieuXuatModel->generateId();
            render('kho/xuat_form', [
                'pageTitle' => 'Lập phiếu xuất kho ra quầy',
                'nextId'    => $nextId,
                'errors'    => [$e->getMessage()],
                'old'       => $_POST,
            ]);
        }
    }

    //Chi tiết phiếu xuất.
    public function xuat_detail(): void
    {
        require_permission('kho');

        $id = trim((string) ($_GET['id'] ?? ''));
        $export = $id !== '' ? $this->phieuXuatModel->find($id) : null;

        if ($export === null) {
            flash('error', 'Không tìm thấy phiếu xuất.');
            redirect_to('kho', 'xuat_index');
        }

        render('kho/xuat_detail', [
            'pageTitle' => 'Chi tiết phiếu xuất: ' . $id,
            'export'    => $export,
        ]);
    }

    //------AJAX API-------
    //AJAX: Tìm kiếm hàng hóa (autocomplete)
    public function search_hang_hoa(): void
    {
        require_login();

        $keyword = trim((string) ($_GET['q'] ?? ''));
        $results = $keyword !== '' ? $this->hangHoaModel->search($keyword) : [];

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $results]);
        exit;
    }

    //AJAX: Tự sinh mã lô hàng theo định dạng LH-YYMMDD-MAHH-STT
    public function generate_lo_code(): void
    {
        require_login();

        $maHangHoa = trim((string) ($_GET['ma_hang_hoa'] ?? ''));
        if ($maHangHoa === '') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Mã hàng hóa không được để trống.']);
            exit;
        }

        // Định dạng ngày hiện tại thành YYMMDD (ví dụ: 260609 cho ngày 2026-06-09)
        $dateStr = date('ymd');
        $prefix = "LH-{$dateStr}-{$maHangHoa}-";

        // Tìm mã lô lớn nhất đã có tiền tố này trong CSDL
        $lastCode = $this->loHangModel->getLastLotCodeWithPrefix($prefix);

        if ($lastCode !== null) {
            // Lấy 2 số cuối cùng
            $seq = (int) substr($lastCode, -2);
            $seq++;
        } else {
            $seq = 1;
        }

        $newCode = $prefix . str_pad((string) $seq, 2, '0', STR_PAD_LEFT);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'code' => $newCode]);
        exit;
    }

    //AJAX: Gợi ý FIFO cho xuất kho
    public function suggest_fifo(): void
    {
        require_login();

        $maHangHoa = trim((string) ($_GET['ma_hang_hoa'] ?? ''));
        $qty = (float) ($_GET['qty'] ?? 0.0);

        if ($maHangHoa === '' || $qty <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin.']);
            exit;
        }

        $result = $this->phieuXuatModel->suggestFIFO($maHangHoa, $qty);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $result]);
        exit;
    }


    //--------VALIDATION---------

    private function validateNhap(array $header, array $details): array
    {
        $errors = [];

        if ($header['ma_nha_cung_cap'] === '') {
            $errors[] = 'Vui lòng chọn Nhà cung cấp.';
        }

        if (empty($details)) {
            $errors[] = 'Phiếu nhập phải có ít nhất 1 mặt hàng.';
        }

        $today = date('Y-m-d');

        foreach ($details as $i => $d) {
            $row = $i + 1;

            if ($d['so_luong'] <= 0) {
                $errors[] = "Dòng {$row}: Số lượng phải lớn hơn 0.";
            }

            // Kiểm tra hàng đóng gói hay hàng cân
            $hangHoa = $this->hangHoaModel->findById($d['ma_hang_hoa']);
            if ($hangHoa !== null) {
                if (empty($hangHoa['ma_tem_can'])) {
                    // Hàng đóng gói, bắt buộc số nguyên
                    if (fmod((float) $d['so_luong'], 1.0) !== 0.0) {
                        $errors[] = "Dòng {$row}: Sản phẩm '{$hangHoa['ten_hang_hoa']}' là hàng đóng gói, số lượng phải là số nguyên.";
                    }
                }
            }

            if ($d['don_gia_nhap'] <= 0) {
                $errors[] = "Dòng {$row}: Đơn giá nhập phải lớn hơn 0.";
            }

            if ($d['ma_lo_hang'] === '') {
                $errors[] = "Dòng {$row}: Mã Lô là bắt buộc.";
            }

            if ($d['ngay_san_xuat'] === '') {
                $errors[] = "Dòng {$row}: Ngày sản xuất là bắt buộc.";
            }

            if ($d['han_su_dung'] === '') {
                $errors[] = "Dòng {$row}: Hạn sử dụng là bắt buộc.";
            }

            if ($d['han_su_dung'] !== '' && $d['han_su_dung'] <= $today) {
                $errors[] = "Dòng {$row}: Hạn sử dụng phải sau ngày hiện tại. Không cho phép nhập hàng đã hết hạn.";
            }

            if ($d['ngay_san_xuat'] !== '' && $d['han_su_dung'] !== '' && $d['ngay_san_xuat'] >= $d['han_su_dung']) {
                $errors[] = "Dòng {$row}: Ngày sản xuất phải trước Hạn sử dụng.";
            }

            if ($d['ngay_san_xuat'] !== '' && $d['ngay_san_xuat'] > $today) {
                $errors[] = "Dòng {$row}: Ngày sản xuất không được sau ngày hiện tại.";
            }
        }

        return $errors;
    }

    private function validateXuat(array $details): array
    {
        $errors = [];

        if (empty($details)) {
            $errors[] = 'Phiếu xuất phải có ít nhất 1 mặt hàng.';
        }

        foreach ($details as $i => $d) {
            $row = $i + 1;

            if ($d['so_luong'] <= 0) {
                $errors[] = "Dòng {$row}: Số lượng phải lớn hơn 0.";
            }

            // Kiểm tra hàng đóng gói hay hàng cân
            $hangHoa = $this->hangHoaModel->findById($d['ma_hang_hoa']);
            if ($hangHoa !== null) {
                if (empty($hangHoa['ma_tem_can'])) {
                    // Hàng đóng gói, bắt buộc số nguyên
                    if (fmod((float) $d['so_luong'], 1.0) !== 0.0) {
                        $errors[] = "Dòng {$row}: Sản phẩm '{$hangHoa['ten_hang_hoa']}' là hàng đóng gói, số lượng phải là số nguyên.";
                    }
                }
            }

            // Kiểm tra tồn kho
            $tonKho = $this->loHangModel->getTotalStockInKho($d['ma_hang_hoa']);
            if ($d['so_luong'] > $tonKho) {
                $errors[] = "Dòng {$row}: Tồn kho không đủ. Yêu cầu: {$d['so_luong']}, Tồn kho: {$tonKho}.";
            }
        }

        return $errors;
    }
}
