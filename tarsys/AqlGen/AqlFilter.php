<?php

namespace tarsys\AqlGen;

/**
 * Class to generate filter conditions
 *
 * @author Tarsis Lima
 */
class AqlFilter
{
    public $conditions = [];

    const AND_OPERATOR = '&&';
    const OR_OPERATOR = '||';

    protected $params = [];

    /**
     * Add a first condition
     *
     * @param string $condition
     * @param Array $params
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
     */
    public function filter($condition)
    {
        return $this->andFilter($condition);
    }

    /**
     * Add filter with AND operator
     *
     * @param string $condition
     * @param Array $params
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
     * @param array $params
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
        $query = '';
        foreach ($this->conditions as $i => $conditions) {
            foreach ($conditions as $operator => $condition) {
                if ($i > 0) {
                    $query .= ' ' . $operator . ' ';
                }
                $query .= $condition . ' ';
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
