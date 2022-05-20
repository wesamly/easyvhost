<?php

namespace App\Listeners;

use App\Events\HostCreated;
use App\Events\HostDeleted;
use App\Events\HostUpdated;
use App\Models\Host;
use App\VirtualHost\VirtualHostHook;
use App\VirtualHost\VirtualHostModelParser;
use App\VirtualHost\VirtualHostWriter;

class VHostsUpdater
{
    /**
     * Update VirtualHost config files
     *
     * @param object $event
     * @return void
     */
    public function updateVHosts($event)
    {
        
        $host = $event->host;
        
        $hosts = Host::get();
        // Default Location
        // TODO: use flysystem?
        $fp = fopen('/usr/local/etc/httpd/extra/httpd-vhosts-default.conf', 'w');

        foreach ($hosts as $host) {
            // TODO: check if have custom location tag

            $vhostParser = new VirtualHostModelParser($host);
            fwrite($fp, $vhostParser->getVirtualHostBlock());

        }

        fclose($fp);

    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        return [
            HostCreated::class => 'updateVHosts',
            HostUpdated::class => 'updateVHosts',
            HostDeleted::class => 'updateVHosts',
        ];
    }
}