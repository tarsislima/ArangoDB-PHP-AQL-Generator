<?php

namespace tarsys\AqlGen;

/**
 * Class to generate filter conditions
 *
 * @author Tarsis Lima
 */
class AqlFilter
{
    public $params = [];
    public $conditions = [];

    const AND_OPERATOR = '&&';
    const OR_OPERATOR = '||';

    /**
     * Add a first condition
     * @param string $condition
     * @param Array $params
     */
    public function __construct($condition, $params=[])
    {
        $this->andFilter($condition, $params);
    }

    /**
     * * add AND condition 
     * @param string $condition
     * @param Array $params
     */
    public function andFilter($condition, $params =[])
    {
        $this->setParams($params);
        $this->conditions[] = [self::AND_OPERATOR => $condition];
    }

    /**
     * add OR condition 
     * @param string $condition
     * @param array $params
     */
    public function orFilter($condition, $params=[])
    {
        $this->setParams($params);
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
            if ($i > 0) {
                $query .= ' ' . $operator . ' ';
            }
            foreach ($conditions as $operator => $condition) {
                if (is_string($condition)) {
                    $query .= $condition . ' ';
                }
                if (is_array($condition)) {
                    //and 
                    foreach ($condition as $key => $value) {
                        $query .= $key . ' == ' . $value . ' ';
                    }
                }

                if ($condition instanceof AqlFilter) {
                    $query .= '(' . $condition->get() . ')';
                }
            }
        }
        return $query;
    }
    
    /**
     * array of params to bind
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * add params to bind
     * @param array $params
     */
    public function setParams($params) 
    {
        $this->params =  array_merge($this->params, $params);
    }
}
