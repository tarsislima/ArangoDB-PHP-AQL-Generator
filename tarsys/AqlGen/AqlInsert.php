<?php

namespace tarsys\AqlGen;

/**
 * Class to implement INSERT Operation
 *
 * @author Tarsis Lima
 */
class AqlInsert extends AbstractAql
{
    use OptionsTrait;

    const OPERATOR = 'INSERT';

    protected $document;
    protected $collection;

    /**
     * AqlInsert constructor.
     * @param mixed $document
     * @param string $collection
     */
    public function __construct($document, $collection)
    {
        $this->document = $document;
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function get()
    {
        $this->document = $this->normalizeDocument($this->document);
        $result = self::OPERATOR . " {$this->document} IN {$this->collection} {$this->options}";
        return $result;
    }
}
