<?php

namespace Interpro\Entrance\Command\Artisan;

use Illuminate\Console\Command;
use Interpro\Entrance\Contracts\CommandAgent\SyncAgent;

class Synchronize extends Command
{
    private $syncAgent;

    /**
     * @var string
     */
    protected $signature = 'sync {type}';

    /**
     * @var string
     */
    protected $description = 'Синхронизировать конфигурацию для типа данных с записями БД';

    /**
     * @return void
     */
    public function __construct(SyncAgent $syncAgent)
    {
        parent::__construct();

        $this->syncAgent = $syncAgent;
    }

    /**
     *
     * @return mixed
     */
    public function handle()
    {
        $type_name = $this->argument('type');

        $this->info('Синхронизация '.$type_name.' начата');

        if($type_name === 'all')
        {
            $this->syncAgent->syncAll();
        }
        elseif($type_name === 'predefined')
        {
            $this->syncAgent->syncPredefinedGroupItems();
        }
        else
        {
            $this->syncAgent->sync($type_name);
        }

        $this->info('Синхронизация '.$type_name.' закончена');
    }


}
