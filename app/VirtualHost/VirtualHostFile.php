<?php

namespace App\VirtualHost;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class VirtualHostFile
{
    private Filesystem $disk;

    private bool $fileExists = false;

    public function __construct(private string $filePath)
    {

        // TODO: add support for FTP/SFTP using Laravel Storage
        $disk = Storage::disk('vhosts_dir');

        $fileExists = ! empty($this->filePath) && $disk->exists($this->filePath);

        if ($fileExists) {
            // Reset Content
            $disk->put($this->filePath, '');
        }

        $this->disk = $disk;
        $this->fileExists = $fileExists;
    }

    /**
     * Write content to file
     */
    public function write(string $content)
    {
        if ($this->fileExists) {
            $this->disk->append($this->filePath, $content);
        }
    }
}
