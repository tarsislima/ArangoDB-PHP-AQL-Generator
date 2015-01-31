<?php

namespace tarsys\AqlGen;

/**
 * Class to amply FUNCTIONS
 *
 * @author Tarsis Lima
 */
class AqlFunction extends AbstractAql
{
    public $query = '';

    public function __construct($functionName, Array $args)
    {
        if (empty($args)) {
            throw new \Exception('Expected 1 or more arguments.');
        }

        foreach ($args as $i => $arg) {
            if ($arg instanceof AbstractAql) {
                $this->bindParams($arg->getParams());
                $arg->resetParams();
                $args[$i] = '(' . $arg->get() . ')';
            }
        }
        $this->query = $functionName . ' (' . implode(', ', $args) . ')';
    }

    public function get()
    {
        return $this->query;
    }
}
