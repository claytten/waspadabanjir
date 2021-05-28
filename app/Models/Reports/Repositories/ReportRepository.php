<?php

namespace App\Models\Reports\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Reports\Report;
use App\Models\Reports\Exceptions\ReportNotFoundException;
use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Models\Tools\UploadableTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class ReportRepository extends BaseRepository implements ReportRepositoryInterface
{
    use UploadableTrait;
    /**
     * ReportRepository constructor.
     *
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        parent::__construct($report);
        $this->model = $report;
    }

    /**
     * List all the reports
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listReports(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return $this->model->orderBy($order, $sort)->get()->except($except);
    }

    /**
     * Create the Report
     *
     * @param array $data
     *
     * @return Report
     */
    public function createReport(array $data): Report
    {
        try {
            if(isset($data['phone'])) {
                if(substr($data['phone'],0,3) == '+62') {
                    $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
                } else if(substr($data['phone'],0,1) == '0') {
                    $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
                } else {
                    $data['phone'] = "+62".$data['phone'];
                }
            }
            return $this->model->create($data);
        } catch (QueryException $e) {
            throw new ReportNotFoundException($e);
        }
    }

    /**
     * Find the report by id
     *
     * @param int $id
     *
     * @return Report
     */
    public function findReportById(int $id): Report
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ReportNotFoundException;
        }
    }

    /**
     * Update report
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateReport(array $params): bool
    {
        if(isset($params['phone'])) {
            if(substr($params['phone'],0,3) == '+62') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else if(substr($params['phone'],0,1) == '0') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else {
                $params['phone'] = "+62".$params['phone'];
            }
        }
        return $this->model->update($params);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteReport() : bool
    {
        return $this->delete();
    }

    /**
     * Creating Report By Whatsapp
     * 
     * @return string
     */
    public function storeReportWhatsapp(array $data) : string
    {
        $this->createReport($data);
        Cache::forget($data['from']);
        return "Terima Kasih sudah memberikan laporan.\nLaporan kamu akan diperiksa admin secepatnya\n\nSilahkan ketik *menu* jika ingin menampilkan daftar layanan portal banjir.";
    }
}
