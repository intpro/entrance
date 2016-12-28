<?php

namespace Interpro\Entrance\Contracts\CommandAgent;

interface DestructAgent
{
    /**
     * @param string $type_name
     * @param int $id
     *
     * @return void
     */
    public function delete($type_name, $id = 0);
}
