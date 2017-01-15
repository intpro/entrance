<?php

namespace Interpro\Entrance\Agents;

use Illuminate\Support\Facades\App;
use Interpro\Core\Contracts\Mediator\SyncMediator;
use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Contracts\Taxonomy\Types\AType;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\Core\Exception\SyncException;
use Interpro\Entrance\Contracts\CommandAgent\SyncAgent as SyncAgentInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class SyncAgent implements SyncAgentInterface
{
    private $syncMediator;
    private $taxonomy;
    private $consoleOutput;
    private $inConsole = false;

    public function __construct(
        SyncMediator $syncMediator,
        Taxonomy $taxonomy,
        ConsoleOutput $consoleOutput
    )
    {
        $this->syncMediator = $syncMediator;
        $this->taxonomy     = $taxonomy;
        $this->consoleOutput = $consoleOutput;

        $this->inConsole = App::runningInConsole();
    }

    private function syncType(AType $type)
    {
        $typeMode = $type->getMode();

        if($typeMode !== TypeMode::MODE_A)
        {
            throw new SyncException('Агент синхронизации может синхронизировать только тип (A) уровня, передан тип:'.$type->getName().'('.$typeMode.')!');
        }

        $family = $type->getFamily();

        $synchronizer = $this->syncMediator->getASynchronizer($family);

        $synchronizer->sync($type);
    }

    /**
     * @param string $type_name
     *
     * @return void
     */
    public function sync($type_name)
    {
        if(!is_string($type_name))
        {
            throw new SyncException('Имя типа должно быть задано строкой!');
        }

        $type = $this->taxonomy->getType($type_name);

        $this->syncType($type);
    }

    /**
     * @return void
     */
    public function syncAll()
    {
        $blocks = $this->taxonomy->getBlocks();

        foreach($blocks as $block)
        {
            $this->syncType($block);

            if($this->inConsole)
            {
                $this->consoleOutput->writeln('Синхронизирован тип '.$block->getName());
            }
        }

        $groups = $this->taxonomy->getGroups();

        foreach($groups as $group)
        {
            $this->syncType($group);

            if($this->inConsole)
            {
                $this->consoleOutput->writeln('Синхронизирован тип '.$group->getName());
            }
        }
    }
}
