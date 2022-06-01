<?php

namespace App\VirtualHost;

use Illuminate\Support\Facades\Storage;

class VirtualHostFile
{
    private $file;
    private $disk;
    private $fileExists = false;

    /**
     * VirtualHostFile constructor.
     *
     * @param string $file Path to file
     */
    public function __construct($file)
    {
        
        $this->file = $file;
        // TODO: add support for FTP/SFTP using Laravel Storage
        $disk = Storage::build([
            'driver' => 'local',
            'root' => '/',
        ]);
        $fileExists = $disk->exists($file);
        if ($fileExists) {
            // Reset Content
            $disk->put($file, '');
        }

        $this->disk = $disk;
        $this->fileExists = $fileExists;
    }

    /**
     * Write content to file
     *
     * @param string $content
     * @return void
     */
    public function write($content)
    {
        if ($this->fileExists) {
            $this->disk->append($this->file, $content);
        }
    }

}