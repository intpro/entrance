<?php

namespace Interpro\Entrance\Agents;

use Interpro\Core\Contracts\Mediator\UpdateMediator;
use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Ref\ARef;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\Core\Exception\UpdateException;
use Interpro\Entrance\Contracts\CommandAgent\UpdateAgent as UpdateAgentInterface;

class UpdateAgent implements UpdateAgentInterface
{
    private $updateMediator;
    private $taxonomy;

    public function __construct(
        UpdateMediator $updateMediator,
        Taxonomy $taxonomy
    )
    {
        $this->updateMediator = $updateMediator;
        $this->taxonomy       = $taxonomy;
    }

    /**
     * @param string $type_name
     * @param int $id
     * @param array $values
     *
     * @return void
     */
    public function update($type_name, $id = 0, array $values = [])
    {
        if(!is_string($type_name))
        {
            throw new UpdateException('Название А-типа) должно быть задано строкой!');
        }

        if(!is_int($id))
        {
            throw new UpdateException('Id А-типа должно быть задано целым числом!');
        }

        $type = $this->taxonomy->getType($type_name);

        $typeMode = $type->getMode();

        if($typeMode !== TypeMode::MODE_A)
        {
            throw new UpdateException('Агент удаления может удалять только тип (A) уровня, передан тип:'.$type->getName().'('.$typeMode.')!');
        }

        $family = $type->getFamily();

        $updator = $this->updateMediator->getAUpdateExecutor($family);

        $ref = new ARef($type, $id);

        $updator->update($ref, $values);
    }
}
