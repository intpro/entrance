<?php

namespace Interpro\Entrance;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Extractor\Contracts\Selection\Tuner;

class EntranceServiceProvider extends ServiceProvider {

    /**
     * @return void
     */
    public function boot(Dispatcher $dispatcher, Taxonomy $taxonomy, Tuner $tuner)
    {
        //Log::info('Загрузка EntranceServiceProvider');

        $this->initDefaultSelectionUnits($taxonomy, $tuner);
    }

    private function initDefaultSelectionUnits(Taxonomy $taxonomy, Tuner $tuner)
    {
        $groups = $taxonomy->getGroups();

        foreach($groups as $group)
        {
            $tuner->initSelection($group, 'group');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Log::info('Регистрация EntranceServiceProvider');

        $this->app->singleton(
            'Interpro\Entrance\Contracts\Extract\ExtractAgent',
            'Interpro\Entrance\Agents\ExtractAgent'
        );

        $this->app->singleton(
            'Interpro\Entrance\Contracts\CommandAgent\InitAgent',
            'Interpro\Entrance\Agents\InitAgent'
        );

        $this->app->singleton(
            'Interpro\Entrance\Contracts\CommandAgent\SyncAgent',
            'Interpro\Entrance\Agents\SyncAgent'
        );

        $this->app->singleton(
            'Interpro\Entrance\Contracts\CommandAgent\UpdateAgent',
            'Interpro\Entrance\Agents\UpdateAgent'
        );

        $this->app->singleton(
            'Interpro\Entrance\Contracts\CommandAgent\DestructAgent',
            'Interpro\Entrance\Agents\DestructAgent'
        );

    }

}
