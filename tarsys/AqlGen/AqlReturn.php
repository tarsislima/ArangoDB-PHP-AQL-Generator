<?php

namespace tarsys\AqlGen;

/**
 * Class to implement UPDATE Operation
 *
 * @author Tarsis Lima
 */
class AqlReturn
{
    const OPERATOR = 'RETURN';

    protected $document;
    protected $collection;
    protected $data = null;

    public function __construct($document)
    {
        $this->document = $document;
    }

    public function get()
    {
        if (is_array($this->document)) {
            $this->document = json_encode($this->document);
        }

        return self::OPERATOR . " {$this->document}";
    }
}
