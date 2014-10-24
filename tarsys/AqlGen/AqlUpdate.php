<?php

namespace tarsys\AqlGen;

/**
 * Class to implement UPDATE Operation
 *
 * @author Tarsis Lima
 */
class AqlUpdate extends AbstractAql
{
    const OPERATOR = 'UPDATE';

    protected $document;
    protected $collection;
    protected $data = null;

    public function __construct($document, $collection, Array $data)
    {
        $this->document = $document;
        $this->collection = $collection;
        $this->data = $data;
    }

    public function get()
    {
        if(is_array($this->document)) {
            $this->document = json_encode($this->document);
        }
        $this->data = json_encode($this->data);

        return Self::OPERATOR . " {$this->document} WITH {$this->data} IN {$this->collection}";
    }
}
