<?php
declare(strict_types=1);

require_once APP_ROOT . '/models/BaoCaoModel.php';

class BaoCaoController
{
    private BaoCaoModel $baoCaoModel;

    public function __construct()
    {
        $this->baoCaoModel = new BaoCaoModel();
    }

    public function index(): void
    {
        require_permission('bao-cao');

        $today = date('Y-m-d');
        $firstDay = date('Y-m-01');
        $from = $this->normalizeDate((string) ($_GET['from'] ?? $firstDay), $firstDay);
        $to = $this->normalizeDate((string) ($_GET['to'] ?? $today), $today);

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $expiringDays = max(1, min(180, (int) ($_GET['expiring_days'] ?? 30)));

        render('bao_cao/index', [
            'pageTitle' => 'Báo cáo',
            'filters' => [
                'from' => $from,
                'to' => $to,
                'expiring_days' => $expiringDays,
            ],
            'summary' => $this->baoCaoModel->revenueSummary($from, $to, $expiringDays),
            'revenueByDay' => $this->baoCaoModel->revenueByDay($from, $to),
            'revenueByEmployee' => $this->baoCaoModel->revenueByEmployee($from, $to),
            'expiringLots' => $this->baoCaoModel->expiringLots($expiringDays),
            'lossReports' => $this->baoCaoModel->lossReports($from, $to),
            'inventoryLots' => $this->baoCaoModel->inventoryLots(),
            'inventorySummary' => $this->baoCaoModel->inventorySummary($expiringDays),
        ]);
    }

    private function normalizeDate(string $value, string $fallback): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if ($date && $date->format('Y-m-d') === $value) {
            return $value;
        }

        return $fallback;
    }
}
