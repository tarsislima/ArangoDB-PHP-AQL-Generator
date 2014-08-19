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

    /**
     * Add a first condition
     *
     * @param string $condition
     * @param Array $params
     */
    public function __construct($condition)
    {
        $this->andFilter($condition);
    }

    /**
     * Add filter with AND operator
     *
     * @param string $condition
     * @param Array $params
     */
    public function andFilter($condition)
    {
        $this->conditions[] = [self::AND_OPERATOR => $condition];
    }

    /**
     * Add filter with OR operator
     *
     * @param string $condition
     * @param array $params
     */
    public function orFilter($condition)
    {
        $this->conditions[] = [self::OR_OPERATOR => $condition];
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
}
