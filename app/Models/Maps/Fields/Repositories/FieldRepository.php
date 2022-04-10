<?php

namespace App\Models\Maps\Fields\Repositories;

use App\Models\Maps\FieldDetailLocations\FieldDetailLocation;
use App\Models\Maps\FieldImages\FieldImage;
use Jsdecena\Baserepo\BaseRepository;
use App\Models\Maps\Fields\Field;
use App\Models\Maps\Fields\Exceptions\FieldNotFoundException;
use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Tools\UploadableTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;

class FieldRepository extends BaseRepository implements FieldRepositoryInterface
{
    use UploadableTrait;
    /**
     * FieldRepository constructor.
     *
     * @param Field $field
     */
    public function __construct(Field $field)
    {
        parent::__construct($field);
        $this->model = $field;
    }

    /**
     * List all the fields
     *
     * @param string $date_in
     * @param string $date_out
     *
     * @return Collection
     */
    public function listFields(string $date_in = '', string $date_out = '') : Collection
    {
        if($date_in === $date_out) {
            $fields_today = $this->model->where('date_in', 'LIKE', '%'.$date_in.'%')->get();
            $fields_doing = $this->model->where('date_out', null)->get();
            return $fields_today->merge($fields_doing);
        } else {
            return $this->model->whereBetween('date_in', [$date_in, $date_out])->get();
        }
    }

    /**
     * List all the field for public
     * 
     * @param string $date_in
     * 
     * @return Collection
     */
    public function listFieldsPublic(string $date_in = ''): Collection
    {
        $fields_today = $this->model->where('date_in', 'LIKE', '%'.$date_in.'%')->where('status', 1)->get();
        $fields_doing = $this->model->where('date_out', null)->where('status', 1)->get();
        return $fields_today->merge($fields_doing);
    }

    /**
     * Create the Field
     *
     * @param array $data
     *
     * @return Field
     */
    public function createField(array $data): Field
    {
        try {
            $field = $this->model->create($data);
            $this->createDetailLocation($field->id, $data['locations']);
            return $field;
        } catch (QueryException $e) {
            throw new FieldNotFoundException($e);
        }
    }

    /**
     * Create detail location at field detail locations table
     * 
     * @param int $id
     * @param array $collection
     * 
     * @return bool
     */
    private function createDetailLocation(int $id, array $collection) {
        foreach($collection as $data) {
            $detailLoc = new FieldDetailLocation([
                'field_id'  => $id,
                'district'  => $data[0],
                'village'   => $data[1]
            ]);
            $detailLoc->save();
        }
    }

    /**
     * Combine date and time to one line
     * 
     * @param string $date
     * @param string $time 
     */
    public function getDateAttribute($date, $time)
    {
        return Carbon::parse($date.' '.$time)->format('Y-m-d H:i:s');
    }

    /**
     * Get Date on format
     * 
     * @param string $date
     */
    public function convertDateAttribute($date)
    {
        return Carbon::parse($date)->format('d-m-Y');
    }

    /**
     * Get Time on format
     * 
     * @param string $time
     */
    public function convertTimeAttribute($time)
    {
        return Carbon::parse($time)->format('H:i');
    }

    /**
     * Find the field by id
     *
     * @param int $id
     *
     * @return Field
     */
    public function findFieldById(int $id): Field
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new FieldNotFoundException;
        }
    }

    /**
     * Find all field filtered by name address
     * 
     * @param string $address
     * 
     * @return collection or null
     */
    public function findFieldByAddress(string $address)
    {
        return FieldDetailLocation::where('district', 'LIKE', "%{$address}%")->distinct()->get(['field_id']);
    }

    /**
     * Update field
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateField(array $params): bool
    {
        $field = $this->model->update($params);
        $this->model->detailLocations()->delete();
        $this->createDetailLocation($this->model->id, $params['locations']);
        return $field;
    }
    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteField() : bool
    {
        return $this->delete();
    }

    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function saveMapImages(Collection $collection)
    {
        $collection->each(function (UploadedFile $file) {
            $filename = $this->uploadOne($file, 'maps');
            $fieldImage = new FieldImage([
                'field_id'  => $this->model->id,
                'src'       => $filename
            ]);
            $this->model->images()->save($fieldImage);
        });
    }

    /**
     * Destroye File on Storage
     *
     * @param Collection $collection
     *
     *
     */
    public function deleteFiles(Collection $collection)
    {
        $collection->each(function ($file) {
            File::delete("storage/{$file->src}");
        });
    }

    /**
     * Destroye File on Storage
     *
     * @param string $get_data
     *
     */
    public function deleteFile(string $get_data)
    {
        return File::delete("storage/{$get_data}");
    }

    /**
     * Listing data flood on Klaten Regency
     * 
     * @return string
     */
    public function listFieldsAndGeo(): string
    {
      $fields_today = $this->model->where('date_in', 'LIKE', '%'.Carbon::now()->toDateString().'%')->where('status', 1)->get();
      $fields_doing = $this->model->where('date_out', null)->where('status', 1)->get();
      $fields = $fields_today->merge($fields_doing);
      if(count($fields) > 0) {
        $message = "--MENU BANJIR Hari Ini--\nBerikut daftar banjir terkini di Kabupaten Klaten: \n";
        $coundColumn = 1;
        foreach($fields as $item) {
            $message .= $this->combineFieldDoingToday($item, $coundColumn);

            $coundColumn += 1;
        }
      } else {
        $message = "Sementara belum ada berita banjir di Kabupaten Klaten.";
      }
      
      return $message;
    }

    private function combineFieldDoingToday($item, $indexItem)
    {
      $message = '';
      $totalVictims = $item->deaths + $item->injured + $item->losts;
      $detailFields = route('maps.show', $item->id);
      $date_in = $this->convertDateAttribute($item->date_in);
      $date_in_time = $this->convertTimeAttribute($item->date_in);
      $date_out_time = ($item->date_out !== null ? $this->convertTimeAttribute($item->date_out) : false);
      $date_out = $item->date_out !== null ? $date_out_time.' WIB, '.$this->convertDateAttribute($item->date_out) : 'Sedang Berlangsung';
      $locationCount = $item->detailLocations->count();
      
      $message .= "\nArea banjir {$indexItem}";
      $message .= "\n  -Jumlah Korban : {$totalVictims}";
      $message .= "\n  -Tanggal Awal Kejadian : {$date_in_time} WIB, {$date_in}";
      $message .= "\n  -Tanggal Akhir Kejadian : {$date_out}";
      $message .= "\n  -Jumlah Kelurahan yang terdampak: {$locationCount} Kelurahan";
      $message .= "\n  -Berita banjir lebih rinci: {$detailFields}";

      return $message;
    }

    public function broadcastField(object $item, string $location): string
    {
      $message = '';
      $name = strtolower($location);
      $totalVictims = $item->deaths + $item->injured + $item->losts;
      //$detailFields = route('maps.show', $item->id);
      $date_in = $this->convertDateAttribute($item->date_in);
      $date_in_time = $this->convertTimeAttribute($item->date_in);
      $date_out_time = ($item->date_out !== null ? $this->convertTimeAttribute($item->date_out) : false);
      $date_out = $item->date_out !== null ? $date_out_time.' WIB, '.$this->convertDateAttribute($item->date_out) : 'Sedang Berlangsung';
      $locationCount = $item->detailLocations->count();
      $message = "--Update Data Terbaru--\nTelah terjadi banjir di daerah Kecamatan {$name}:";
      $message .= "\n  -Jumlah Korban : {$totalVictims}";
      $message .= "\n  -Tanggal Awal Kejadian : {$date_in_time} WIB, {$date_in}";
      $message .= "\n  -Tanggal Akhir Kejadian : {$date_out}";
      $message .= "\n  -Jumlah Kelurahan yang terdampak: {$locationCount} Kelurahan";
      //$message .= "\n  -Berita banjir lebih rinci: {$detailFields}";

      return $message;
    }
}
