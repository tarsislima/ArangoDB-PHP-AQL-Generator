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
        if(is_array($this->document)) {
            $this->document = json_encode($this->document);
        }
        return self::OPERATOR . " {$this->document} IN {$this->collection}";
    }
}
