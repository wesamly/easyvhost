<?php

namespace App\Console\Commands;

use App\Events\HostUpdated;
use App\Models\Host;
use Illuminate\Console\Command;

class RewriteVhosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vhosts:rewrite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers virtual hosts rewrite';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $host = Host::first();
        HostUpdated::dispatch($host);

        return 0;
    }
}
