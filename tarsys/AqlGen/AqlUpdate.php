<?php
namespace tarsys\AqlGen;
/**
 * Class to implement UPDATE Operation
 *
 * @package tarsys\AqlGen
 * @author Vinicius Cruz
 */
class AqlUpdate extends AbstractAql
{
    const OPERATOR = 'UPDATE';

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
        $this->document = $this->normalizeDocument($this->document);
        $this->changedAttributes = $this->normalizeDocument($this->changedAttributes);

        $result = self::OPERATOR . " {$this->document} WITH {$this->changedAttributes} IN {$this->collection} ";
        return $result;
    }
}