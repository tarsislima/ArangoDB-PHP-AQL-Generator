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

    protected $params = [];

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
        return self::OPERATOR . ' ' . $this->getConditionsString() . self::LINE_SEPARATOR;
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

    /**
     * Set a list of params to bind
     *
     * @param Array $params Key => values of variables to bind
     * eg: $query->bindParams(array('name' => 'john', 'status' => 'OK'));
     * @return string
     */
    public function bindParams($params)
    {
        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }
        return $this;
    }

    /**
     * Get all params to bind
     * @return Array
     */
    public function getParams()
    {
        return $this->params;
    }
}
