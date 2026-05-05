<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/HoaDonModel.php';

class BanHangController
{
    private HoaDonModel $hoaDonModel;

    public function __construct()
    {
        $this->hoaDonModel = new HoaDonModel();
    }

    public function index(): void
    {
        $this->pos();
    }

    //Giao diện bán hàng
    public function pos(): void
    {
        require_permission('ban-hang');

        render('ban_hang/pos', [
            'pageTitle' => 'POS bán hàng',
        ]);
    }

    //QUét mã vạch
    public function scan_barcode(): void
    {
        require_permission('ban-hang');

        $payload = $this->readJsonPayload();

        try {
            $item = $this->hoaDonModel->resolveBarcode((string) ($payload['barcode'] ?? ''));
            $this->json(['success' => true, 'data' => $item]);
        } catch (Throwable $exception) {
            $this->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    //Thanh toán hóa đơn
    public function checkout(): void
    {
        require_permission('ban-hang');

        $payload = $this->readJsonPayload();
        $currentUser = current_user();

        try {
            $invoiceId = $this->hoaDonModel->checkout(
                $payload,
                $currentUser['ma_nhan_vien'] ?? null
            );

            $this->json([
                'success' => true,
                'message' => 'Thanh toán thành công.',
                'ma_hoa_don' => $invoiceId,
                'detail_url' => url_for('ban-hang', 'detail', ['id' => $invoiceId]),
            ]);
        } catch (Throwable $exception) {
            $this->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    //Hiển thị chi tiết hóa đơn
    public function detail(): void
    {
        require_permission('ban-hang');

        //Lấy mã hóa đơn
        $invoiceId = trim((string) ($_GET['id'] ?? ''));
        //Tìm hóa đơn theo mã
        $invoice = $invoiceId !== '' ? $this->hoaDonModel->find($invoiceId) : null;

        //Nếu không tìm thấy hóa đơn thì chuyển hướng về POS
        if ($invoice === null) {
            flash('error', 'Không tìm thấy hóa đơn.');
            redirect_to('ban-hang', 'pos');
        }

        //Hiển thị chi tiết hóa đơn
        render('ban_hang/detail', [
            'pageTitle' => 'Hóa đơn ' . $invoiceId,
            'invoice' => $invoice,
        ]);
    }

    //Đọc dữ liệu JSON từ request
    private function readJsonPayload(): array
    {
        $rawBody = file_get_contents('php://input');
        $payload = json_decode($rawBody !== false ? $rawBody : '', true);

        return is_array($payload) ? $payload : [];
    }

    //Gửi dữ liệu JSON
    private function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
