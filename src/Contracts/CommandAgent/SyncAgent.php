<?php

namespace Interpro\Entrance\Contracts\CommandAgent;

interface SyncAgent
{
    /**
     * @param string $type_name
     *
     * @return void
     */
    public function sync($type_name);

    /**
     * @return void
     */
    public function syncPredefinedGroupItems();

    /**
     * @return void
     */
    public function syncAll();
}
