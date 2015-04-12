<?php namespace App\Paka\Transformers;


abstract class Transformer {

    /**
     * Transforms a collection of paka items
     *
     * @param array $items
     * @return array transformed items
     */
    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }

    public abstract function transform($item);
}