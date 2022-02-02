<?php

namespace App\Listeners;

use App\Events\HostCreated;
use App\Events\HostDeleted;
use App\Events\HostUpdated;

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