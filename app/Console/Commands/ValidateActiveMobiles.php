<?php

namespace App\Console\Commands;

use App\BL\Mobile\MobileBL;
use App\Models\Mobile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ValidateActiveMobiles extends Command
{
    private $mobileBL;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:validate-inactives';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea programada encargada de inactivar los conductores, que no reportan su ubicación';

    public function __construct(MobileBL $mobileBL)
    {
        parent::__construct();
        $this->mobileBL =    $mobileBL;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->mobileBL->recordMobileInactivity();
    }
}
