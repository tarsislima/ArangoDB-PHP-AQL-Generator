<?php

namespace tarsys\AqlGen\InnerOperations;

use tarsys\AqlGen\AbstractAql;
use tarsys\AqlGen\AqlGen;

/**
 * Class to implement LET Operation
 *
 * @author Tarsis Lima
 */
class Let extends AbstractAql
{
    const OPERATOR = 'LET';

    protected $variable;
    protected $value;

    public function __construct($variable, $value)
    {
        $this->variable = $variable;
        $this->value = $value;
    }

    public function get()
    {
        if ($this->value instanceof AqlGen) {
            $this->value = "({$this->value})";
        }
        $result = self::OPERATOR . " {$this->variable} = {$this->value}" . PHP_EOL;
        return $result;
    }
}
