<?php

namespace App\Listeners;

use App\Events\HostCreated;
use App\Events\HostDeleted;
use App\Events\HostUpdated;
use App\Events\SettingsUpdated;
use App\Models\Host;
use App\VirtualHost\VirtualHostFile;
use App\VirtualHost\VirtualHostModelParser;
use Illuminate\Events\Dispatcher;

class VHostsUpdater
{
    /**
     * Update VirtualHost config files
     *
     * @param  object  $event
     * @return void
     */
    public function updateVHosts($event)
    {

        $hosts = Host::get();

        $tagFiles = $this->getConfigFiles();
        $foundTags = $this->getUniqueTagsIds($tagFiles);

        // TODO: use flysystem
        $defaultFile = new VirtualHostFile(setting('default_file', ''));

        $vhFiles = [];
        foreach ($tagFiles as $tagFile) {
            foreach ($tagFile->getTagsIds() as $tagId) {
                $vhFiles[$tagId][] = new VirtualHostFile($tagFile->getFile());
            }
        }

        foreach ($hosts as $host) {
            $vhostParser = new VirtualHostModelParser($host);
            // Check if have custom location tag
            $hostTagIds = $host->tags->pluck('id')->toArray();
            if (empty(array_intersect($hostTagIds, $foundTags))) {
                $defaultFile->write($vhostParser->getVirtualHostBlocks());
            } else {
                foreach ($foundTags as $tagId) {
                    if (isset($vhFiles[$tagId])) {
                        foreach ($vhFiles[$tagId] as $vhFile) {
                            $vhFile->write($vhostParser->getVirtualHostBlocks());
                        }
                    }
                }
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [HostCreated::class, HostUpdated::class, HostDeleted::class, SettingsUpdated::class],
            [VHostsUpdater::class, 'updateVHosts']
        );
    }

    /**
     * Get Configured per Tag Virtual Host Config Files
     *
     * @return array
     */
    private function getConfigFiles()
    {
        $configFiles = setting('files', '[]');
        $configFiles = json_decode($configFiles, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $configFiles = [];
        }

        $tagFiles = [];
        foreach ($configFiles as $entry) {
            if (empty($entry['file'])) {
                continue;
            }
            if (isset($entry['tags']) && is_array($entry['tags'])) {

                $tagFiles[] = new class($entry)
                {
                    public $file;

                    public $tags;

                    public function __construct($entry)
                    {
                        $this->file = $entry['file'];
                        $this->tags = $entry['tags'];
                    }

                    public function getFile()
                    {
                        return $this->file;
                    }

                    public function getTagsIds()
                    {
                        return array_column($this->tags, 'id');
                    }
                };

            }
        }

        return $tagFiles;
    }

    /**
     * Get Unique Tags Ids
     *
     * @param  array  $tagFiles
     * @return array
     */
    private function getUniqueTagsIds($tagFiles)
    {
        $ids = [];
        foreach ($tagFiles as $tagFile) {
            $ids = array_merge($ids, $tagFile->getTagsIds());
        }

        return array_unique($ids);
    }
}
