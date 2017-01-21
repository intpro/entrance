<?php

namespace Interpro\Entrance\Agents;

use Interpro\Core\Contracts\Mediator\InitMediator;
use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Contracts\Taxonomy\Types\AType;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\Core\Taxonomy\Enum\TypeRank;
use Interpro\Entrance\Contracts\CommandAgent\InitAgent as InitAgentInterface;
use Interpro\Entrance\Contracts\Extract\ExtractAgent as ExtractAgentInterface;
use Interpro\Entrance\Exception\EntranceException;
use Interpro\Extractor\Collections\BlockCollection;

class InitAgent implements InitAgentInterface
{
    private $initMediator;
    private $taxonomy;
    private $extractAgent;

    public function __construct(
        InitMediator $initMediator,
        Taxonomy $taxonomy,
        ExtractAgentInterface $extractAgent
    )
    {
        $this->initMediator = $initMediator;
        $this->taxonomy     = $taxonomy;
        $this->extractAgent = $extractAgent;
    }

    private function initType(AType $type, array $defaults = [])
    {
        $typeMode = $type->getMode();

        if($typeMode !== TypeMode::MODE_A)
        {
            throw new EntranceException('Агент инициализации может инициализировать только тип (A) уровня, передан тип:'.$type->getName().'('.$typeMode.')!');
        }

        $family = $type->getFamily();

        $initializer = $this->initMediator->getAInitializer($family);

        $ref = $initializer->init($type, $defaults);

        return $ref;
    }

    /**
     * @param string $type_name
     * @param array $defaults
     *
     * @return \Interpro\Extractor\Contracts\Items\AItem
     */
    public function init($type_name, array $defaults = [])
    {
        if(!is_string($type_name))
        {
            throw new EntranceException('Имя типа должно быть задано строкой!');
        }

        $type = $this->taxonomy->getType($type_name);

        if($type->getMode() !== TypeMode::MODE_A)
        {
            throw new EntranceException('Тип '.$type_name.' может быть инициализирован только автоматически в составе (A) типа!');
        }

        $ref = $this->initType($type, $defaults);

        if($type->getRank() === TypeRank::BLOCK)
        {
            $item = $this->extractAgent->getBlock($type_name);
        }
        else
        {
            $item = $this->extractAgent->getGroupItem($type_name, $ref->getId());
        }

        return $item;
    }

    /**
     * @return \Interpro\Extractor\Contracts\Collections\BlockCollection
     */
    public function initBlocks()
    {
        $blocks = $this->taxonomy->getBlocks();

        $blocksCollection = new BlockCollection();

        foreach($blocks as $block)
        {
            $ref = $this->initType($block);
            $blockItem = $this->extractAgent->getBlock($block->getName());
            $blocksCollection->addBlock($blockItem);
        }

        return $blocksCollection;
    }
}
