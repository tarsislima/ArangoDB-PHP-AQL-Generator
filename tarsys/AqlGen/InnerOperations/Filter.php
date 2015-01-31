<?php

namespace tarsys\AqlGen\InnerOperations;

use tarsys\AqlGen\AbstractAql;

/**
 * Class to generate FILTER conditions
 *
 * @author Tarsis Lima
 */
class Filter extends AbstractAql
{
    const OPERATOR = 'FILTER';

    public $conditions = [];

    const AND_OPERATOR = '&&';
    const OR_OPERATOR = '||';

    /**
     * Add a first condition
     *
     * @param string $condition
     */
    public function __construct($condition = null)
    {
        if (!is_null($condition)) {
            $this->andFilter($condition);
        }
    }

    /**
     * Alias to andCondition
     * @param $condition
     * @return $this
     */
    public function filter($condition)
    {
        return $this->andFilter($condition);
    }

    /**
     * Add filter with AND operator
     *
     * @param string $condition
     * @return $this
     */
    public function andFilter($condition)
    {
        if (!is_string($condition)) {
            throw new \InvalidArgumentException('Param "condition" should be a string.');
        }
        $this->conditions[] = [self::AND_OPERATOR => $condition];
        return $this;
    }

    /**
     * Add filter with OR operator
     *
     * @param string $condition
     * @return $this
     */
    public function orFilter($condition)
    {
        if (!is_string($condition)) {
            throw new \InvalidArgumentException('Param "condition" should be a string.');
        }
        $this->conditions[] = [self::OR_OPERATOR => $condition];
        return $this;
    }

    /**
     * return a string of Conditions
     * @return string
     */
    public function get()
    {
        return self::OPERATOR . ' ' . $this->getConditionsString() . PHP_EOL;
    }

    /**
     * Get the conditions parsed to string
     * @return string
     */
    public function getConditionsString()
    {
        $query = '';
        foreach ($this->conditions as $i => $conditions) {
            foreach ($conditions as $operator => $condition) {
                if ($i > 0) {
                    $query .= ' ' . $operator . ' ';
                }
                $query .= $condition;
            }
        }
        return $query;
    }
}
