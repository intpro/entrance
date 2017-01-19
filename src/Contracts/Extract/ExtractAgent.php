<?php

namespace Interpro\Entrance\Contracts\Extract;

interface ExtractAgent
{
    /**
     * @param string $group_name
     * @param string $selection_name
     *
     * @return \Interpro\Extractor\Contracts\Selection\SelectionUnit $selUnit
     */
    public function tuneSelection($group_name, $selection_name = 'group');

    /**
     * @param string $group_name
     * @param string $selection_name
     *
     * @return int
     */
    public function countGroup($group_name, $selection_name = 'group');

    /**
     * @param string $group_name
     * @param string $selection_name
     *
     * @return \Interpro\Entrance\Contracts\Extract\Selection
     */
    public function selectGroup($group_name, $selection_name = 'group');

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
     * @return void
     */
    public function reset();

    /**
     * @param string $group_name
     * @param string $slug_name
     *
     * @return \Interpro\Extractor\Contracts\Items\GroupItem
     */
    public function getBySlug($group_name, $slug_name);
}
