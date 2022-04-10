<?php

namespace App\Models\Tools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait UploadableTrait
{
    /**
     * Upload a single file in the server
     *
     * @param UploadedFile $file
     * @param null $folder
     * @param string $disk
     * @param null $filename
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, $disk = 'public')
    {
        return $file->store($folder, ['disk' => $disk]);
    }
}
