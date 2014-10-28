<?php

namespace tarsys\AqlGen\InnerOperations;

use tarsys\AqlGen\AbstractAql;
use tarsys\AqlGen\AqlGen;

/**
 * Class to implement COLLECT Operation
 *
 * @author Tarsis Lima
 */
class Collect extends AbstractAql
{
    const OPERATOR = 'COLLECT';

    protected $variable;
    protected $value;
    protected $into;

    public function __construct($variable, $value, $into = null)
    {
        $this->variable = $variable;
        $this->value = $value;
        $this->into = $into;
    }

    public function get()
    {
        $result = self::OPERATOR . " {$this->variable} = {$this->value}";
        if (!is_null($this->into)) {
            $result .= ' INTO ' . $this->into;
        }

        return $result . self::LINE_SEPARATOR;
    }
}
