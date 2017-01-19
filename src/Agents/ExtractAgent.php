<?php

namespace Interpro\Entrance\Agents;

use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Ref\ARef;
use Interpro\Core\Taxonomy\Enum\TypeRank;
use Interpro\Entrance\Contracts\Extract\ExtractAgent as ExtractAgentInterface;
use Interpro\Entrance\Exception\EntranceException;
use Interpro\Entrance\Extract\Selection;
use Interpro\Extractor\Collections\BlockCollection;
use Interpro\Extractor\Contracts\Load\Loader;
use Interpro\Extractor\Contracts\Selection\Tuner;

class ExtractAgent implements ExtractAgentInterface
{
    private $loader;
    private $tuner;
    private $taxonomy;

    public function __construct(
        Tuner $tuner,
        Loader $loader,
        Taxonomy $taxonomy
    )
    {
        $this->tuner = $tuner;
        $this->loader = $loader;
        $this->taxonomy = $taxonomy;
    }

    /**
     * @param string $group_name
     * @param string $selection_name
     *
     * @return \Interpro\Extractor\Contracts\Selection\SelectionUnit $selUnit
     */
    public function tuneSelection($group_name, $selection_name = 'group')
    {
        if($this->tuner->selectionExist($group_name, $selection_name))
        {
            return $this->tuner->getSelection($group_name, $selection_name);
        }
        else
        {
            $type = $this->taxonomy->getGroup($group_name);

            return $this->tuner->initSelection($type, $selection_name);
        }
    }

    private function newSelection($group_name, $selection_name)
    {
        if($this->tuner->selectionExist($group_name, $selection_name))
        {
            $unit = $this->tuner->getSelection($group_name, $selection_name);
            $selection = new Selection($this->loader, $unit);
        }
        else
        {
            $type = $this->taxonomy->getType($group_name);
            $unit = $this->tuner->initSelection($type, $selection_name);
            $selection = new Selection($this->loader, $unit);
        }

        return $selection;
    }

    /**
     * @param string $group_name
     * @param string $selection_name
     *
     * @return int
     */
    public function countGroup($group_name, $selection_name = 'group')
    {
        $selection = $this->newSelection($group_name, $selection_name);

        return $selection->count();
    }

    /**
     * @param string $group_name
     * @param string $selection_name
     *
     * @return \Interpro\Entrance\Contracts\Extract\Selection
     */
    public function selectGroup($group_name, $selection_name = 'group')
    {
        $selection = $this->newSelection($group_name, $selection_name);

        return $selection;
    }

    /**
     * @param string $block_name
     *
     * @return \Interpro\Extractor\Contracts\Items\BlockItem
     */
    public function getBlock($block_name)
    {
        $type = $this->taxonomy->getType($block_name);

        if($type->getRank() !== TypeRank::BLOCK)
        {
            throw new EntranceException('Запрошенный элемент не является блоком ('.$block_name.')!');
        }

        $ref = new ARef($type, 0);

        return $this->loader->loadItem($ref);
    }

    /**
     * @return \Interpro\Extractor\Contracts\Collections\BlockCollection
     */
    public function getBlocks()
    {
        $blocks = $this->taxonomy->getBlocks();

        $blocksCollection = new BlockCollection();

        foreach($blocks as $block)
        {
            $blockItem = $this->getBlock($block->getName());
            $blocksCollection->addBlock($blockItem);
        }

        return $blocksCollection;
    }

    /**
     * @param string $group_name
     * @param int $id
     *
     * @return \Interpro\Extractor\Contracts\Items\GroupItem
     */
    public function getGroupItem($group_name, $id)
    {
        $type = $this->taxonomy->getType($group_name);

        if($type->getRank() !== TypeRank::GROUP)
        {
            throw new EntranceException('Запрошенный элемент не является группой ('.$group_name.')!');
        }

        $ref = new ARef($type, $id);

        return $this->loader->loadItem($ref);
    }

    public function reset()
    {
        $this->loader->reset();
        $this->tuner->reset();
    }

    /**
     * @param string $group_name
     * @param string $slug_name
     *
     * @return \Interpro\Extractor\Contracts\Items\GroupItem
     */
    public function getBySlug($group_name, $slug_name)
    {
        $selection = $this->selectGroup($group_name, 'slug_'.$slug_name);
        $selection->eq('slug', $slug_name);
        $item = $selection->get()->first();

        return $item;
    }
}
