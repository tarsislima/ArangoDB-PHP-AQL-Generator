<?php

namespace tarsys\AqlGen;

/**
 * Class to implement REPLACE Operation
 *
 * @author Tarsis Lima
 */
class AqlReplace extends AbstractAql
{
    const OPERATOR = 'REPLACE';

    protected $document;
    protected $collection;
    protected $options;

    /**
     * AqlReplace constructor.
     * @param array|string $document
     * @param string $collection
     * @param array|null $options
     */
    public function __construct($document, $collection, array $options = null)
    {
        $this->document = $document;
        $this->collection = $collection;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function get()
    {
        if (is_array($this->document)) {
            $this->document = json_encode($this->document);
        }

        if (is_array($this->options)) {
            $this->options = json_encode($this->options);
        }
        $result = self::OPERATOR . " {$this->document} IN {$this->collection}";
        if (!empty($this->options)) {
            $result .= " OPTIONS " . $this->options;
        }
        return $result . " ";
    }
}
