<?php

namespace tarsys\AqlGen;

/**
 * Class to implement REPLACE Operation
 *
 * @author Tarsis Lima
 */
class AqlReplace extends AbstractAql
{
    const OPERATOR = 'UPDATE';

    protected $document;
    protected $collection;

    public function __construct($document, $collection)
    {
        $this->document = $document;
        $this->collection = $collection;
    }

    public function get()
    {
        if (is_array($this->document)) {
            $this->document = json_encode($this->document);
        }

        return self::OPERATOR . " {$this->document} WITH {$this->data} IN {$this->collection}";
    }
}
