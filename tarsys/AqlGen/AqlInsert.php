<?php

namespace tarsys\AqlGen;

/**
 * Class to implement INSERT Operation
 *
 * @author Tarsis Lima
 */
class AqlInsert
{
    const OPERATOR = 'INSERT';

    protected $document;
    protected $collection;

    public function __construct($document, $collection)
    {
        $this->document = $document;
        $this->collection = $collection;
    }

    public function get()
    {
        return Self::OPERATOR . " {$this->document} IN {$this->collection}";
    }
}
