<?php

namespace tarsys\AqlGen;

/**
 * Class to implement DELETE Operation
 *
 * @author Tarsis Lima
 */
class AqlReplace extends AbstractAql
{
    const OPERATOR = 'DELETE';

    protected $document;
    protected $collection;
    protected $options;

    public function __construct($document, $collection, $options = null)
    {
        $this->document = $document;
        $this->collection = $collection;
        $this->options = $options;
    }

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
            $result .= "OPTIONS " . $this->options;
        }
        return $result;
    }
}
