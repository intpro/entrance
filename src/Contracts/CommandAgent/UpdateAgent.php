<?php

namespace Interpro\Entrance\Contracts\CommandAgent;

interface UpdateAgent
{
    /**
     * @param string $type_name
     * @param int $id
     * @param array $values
     *
     * @return void
     */
    public function update($type_name, $id = 0, array $values = []);
}
