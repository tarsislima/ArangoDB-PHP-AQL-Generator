<?php

namespace tarsys\AqlGen;

/**
 * Class to implement REPLACE Operation
 *
 * !! This Operation have the same sintax that Update Operation.
 *  only Operator name is not equal. @todo  refactory
 *
 * @author Tarsis Lima
 */
class AqlReplace extends AqlUpdate
{
    const OPERATOR = 'REPLACE';

    protected $document;
    protected $collection;
    protected $changedAttributes;

    /**
     * AqlUpdate constructor.
     * @param $document
     * @param $changedAttributes
     * @param $collection
     */
    public function __construct($document, $changedAttributes, $collection)
    {
        $this->document = $document;
        $this->collection = $collection;
        $this->changedAttributes = $changedAttributes;
    }

    public function get()
    {
        $document = $this->normalizeDocument($this->document);
        $changedAttributes = $this->normalizeDocument($this->changedAttributes);

        $result = self::OPERATOR . " {$document} WITH {$changedAttributes} IN {$this->collection} {$this->options}";

        return $result;
    }
}
