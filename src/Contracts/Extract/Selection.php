<?php

namespace Interpro\Entrance\Contracts\Extract;

interface Selection
{
    /**
     * @return \Interpro\Extractor\Collections\GroupCollection
     */
    public function get();

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function moreThen($field_path, $value, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function lessThan($field_path, $value, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $min_x
     * @param string $max_x
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function inRange($field_path, $min_x, $max_x, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $min_x
     * @param string $max_x
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function notInRange($field_path, $min_x, $max_x, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function eq($field_path, $value, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function notEq($field_path, $value, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function like($field_path, $value, $or_union_name = null);

    /**
     * @param string $field_path
     * @param string $value
     * @param string $or_union_name
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function notLike($field_path, $value, $or_union_name = null);

    /**
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function sortBy($field_name, $order = 'ASC');

    /**
     * @param int $value
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function skip($value);

    /**
     * @param int $value
     *
     * @return \Interpro\Entrance\Extract\Selection
     */
    public function take($value);
}
