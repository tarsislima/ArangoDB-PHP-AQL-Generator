<?php

namespace tarsys\AqlGen;

/**
 * Class to implement INSERT Operation
 *
 * @author Tarsis Lima
 */
class AqlInsert extends AbstractAql
{
    const OPERATOR = 'INSERT';

    protected $document;
    protected $collection;

    /**
     * AqlInsert constructor.
     * @param array $document
     * @param string $collection
     */
    public function __construct(array $document, $collection)
    {
        $this->document = $document;
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function get()
    {
        if (is_array($this->document)) {
            $this->document = json_encode($this->document);
        }

        $result = self::OPERATOR . " {$this->document} IN {$this->collection} ";
        return $result;
    }
}
