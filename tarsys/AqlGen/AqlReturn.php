<?php

namespace tarsys\AqlGen;

/**
 * Class to implement RETURN Operation
 *
 * @author Tarsis Lima
 */
class AqlReturn extends AbstractAql
{
    const OPERATOR = 'RETURN';

    protected $document;
    protected $collection;
    protected $data = null;

    /**
     * AqlReturn constructor.
     * @param array|string $document
     */
    public function __construct($document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function get()
    {
        $this->document = $this->normalizeDocument($this->document);
        return self::OPERATOR . " {$this->document}";
    }
}
