<?php

namespace tarsys\AqlGen;

/**
 * Class to implement REMOVE Operation
 *
 * @author Tarsis Lima
 */
class AqlRemove extends AbstractAql
{
    const OPERATOR = 'REMOVE';

    protected $document;
    protected $collection;

    /**
     * AqlInsert constructor.
     *
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
        /*if (is_string($this->document)) {
            $this->document = '"' . $this->document . '"';
        }

        if (is_array($this->document)) {
            $this->document = json_encode($this->document);
            $this->document = $this->fixDocumentKeywords($this->document);
        }*/
        $this->normalizeDocument();

        $result = self::OPERATOR . " {$this->document} IN {$this->collection} ";
        return $result;
    }
}
