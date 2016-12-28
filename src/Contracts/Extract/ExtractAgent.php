<?php

namespace Interpro\Entrance\Contracts\Extract;

interface ExtractAgent
{
    /**
     * @param string $group_name
     *
     * @return \Interpro\Entrance\Contracts\Extract\Selection
     */
    public function selectGroup($group_name);

    /**
     * @param string $block_name
     *
     * @return \Interpro\Extractor\Contracts\Items\BlockItem
     */
    public function getBlock($block_name);

    /**
     * @return \Interpro\Extractor\Contracts\Collections\BlockCollection
     */
    public function getBlocks();

    /**
     * @param string $group_name
     * @param int $id
     *
     * @return \Interpro\Extractor\Contracts\Items\GroupItem
     */
    public function getGroupItem($group_name, $id);

    /**
     * @param string $group_name
     * @param string $slug_name
     *
     * @return \Interpro\Extractor\Contracts\Items\GroupItem
     */
    public function getBySlug($group_name, $slug_name);
}
