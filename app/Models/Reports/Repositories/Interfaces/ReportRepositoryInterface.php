<?php

namespace App\Models\Reports\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Reports\Report;
use Illuminate\Support\Collection;

interface ReportRepositoryInterface extends BaseRepositoryInterface
{
    public function listReports(string $order = 'id', string $sort = 'desc', $except = []) : Collection;

    public function createReport(array $params) : Report;

    public function findReportById(int $id) : Report;

    public function updateReport(array $params): bool;

    public function deleteReport() : bool;

    public function storeReportWhatsapp(array $data) : string;

    public function reportWhatsapp(): string;

    public function dailyReport(string $date): int;
}
