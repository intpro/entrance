<?php

namespace Interpro\Entrance\Contracts\CommandAgent;

interface InitAgent
{
    /**
     * @param string $type_name
     * @param array $defaults
     *
     * @return \Interpro\Extractor\Contracts\Items\AItem
     */
    public function init($type_name, array $defaults = []);

    /**
     * @return \Interpro\Extractor\Contracts\Collections\BlockCollection
     */
    public function initBlocks();
}
