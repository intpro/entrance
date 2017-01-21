<?php

namespace Interpro\Entrance\Agents;

use Interpro\Core\Contracts\Mediator\DestructMediator;
use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Ref\ARef;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\Entrance\Contracts\CommandAgent\DestructAgent as DestructAgentInterface;
use Interpro\Entrance\Exception\EntranceException;

class DestructAgent implements DestructAgentInterface
{
    private $destructMediator;
    private $taxonomy;

    public function __construct(
        DestructMediator $destructMediator,
        Taxonomy $taxonomy
    )
    {
        $this->destructMediator = $destructMediator;
        $this->taxonomy         = $taxonomy;
    }

    /**
     * @param string $type_name
     * @param int $id
     *
     * @return void
     */
    public function delete($type_name, $id = 0)
    {
        if(!is_string($type_name))
        {
            throw new EntranceException('Название А-типа) должно быть задано строкой!');
        }

        if(!is_int($id))
        {
            throw new EntranceException('Id А-типа должно быть задано целым числом!');
        }

        $type = $this->taxonomy->getType($type_name);

        $typeMode = $type->getMode();

        if($typeMode !== TypeMode::MODE_A)
        {
            throw new EntranceException('Агент удаления может удалять только тип (A) уровня, передан тип:'.$type->getName().'('.$typeMode.')!');
        }

        $family = $type->getFamily();

        $destructor = $this->destructMediator->getADestructor($family);

        $ref = new ARef($type, $id);

        $destructor->delete($ref);
    }

}
