<?php

namespace Interpro\Entrance\Extract;

use Interpro\Entrance\Contracts\Extract\Selection as SelectionInterface;
use Interpro\Extractor\Contracts\Load\Loader;
use Interpro\Extractor\Contracts\Selection\SelectionUnit;

class Selection implements SelectionInterface
{
    private $loader;
    private $unit;

    public function __construct(
        Loader $loader,
        SelectionUnit $unit
    )
    {
        $this->loader = $loader;
        $this->unit = $unit;
    }

    /**
     * @return \Interpro\Extractor\Collections\GroupCollection
     */
    public function get()
    {
        $type = $this->unit->getType();
        $name = $this->unit->getName();

        return $this->loader->loadGroupCollection($type, $name);
    }

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function moreThen($field_path, $value, $or_union_name = null)
    {
        $this->unit->moreThen($field_path, $value, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function lessThan($field_path, $value, $or_union_name = null)
    {
        $this->unit->lessThan($field_path, $value, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $min_x
     * @param string $max_x
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function inRange($field_path, $min_x, $max_x, $or_union_name = null)
    {
        $this->unit->inRange($field_path, $min_x, $max_x, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $min_x
     * @param string $max_x
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function notInRange($field_path, $min_x, $max_x, $or_union_name = null)
    {
        $this->unit->notInRange($field_path, $min_x, $max_x, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function eq($field_path, $value, $or_union_name = null)
    {
        $this->unit->eq($field_path, $value, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function notEq($field_path, $value, $or_union_name = null)
    {
        $this->unit->notEq($field_path, $value, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function like($field_path, $value, $or_union_name = null)
    {
        $this->unit->like($field_path, $value, $or_union_name);

        return $this;
    }

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function notLike($field_path, $value, $or_union_name = null)
    {
        $this->unit->notLike($field_path, $value, $or_union_name);

        return $this;
    }

    /**
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function sortBy($field_name, $order = 'ASC')
    {
        $this->unit->sortBy($field_name, $order);

        return $this;
    }

    /**
     * @param int $value
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function skip($value)
    {
        $this->unit->skip($value);

        return $this;
    }

    /**
     * @param int $value
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function take($value)
    {
        $this->unit->take($value);

        return $this;
    }
}
