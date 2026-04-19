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
                'so_luong'      => (int) ($soLuongArr[$i] ?? 0),
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

}
