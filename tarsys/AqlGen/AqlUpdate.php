<?php

namespace tarsys\AqlGen;

/**
 * Class to implement UPDATE Operation
 *
 * @author Tarsis Lima
 */
class AqlUpdate
{
    const OPERATOR = 'UPDATE';

    protected $document;
    protected $collection;
    protected $data = null;

    public function __construct($document, $collection, $data)
    {
        $this->document = $document;
        $this->collection = $collection;
        $this->data = $data;
    }

    public function get()
    {
        return Self::OPERATOR . " {$this->document} WITH {$this->data} IN {$this->collection}";
    }
}
