<?php

namespace tarsys\AqlGen;

/**
 * Class to build AQL strings
 *
 * @author Társis Lima
 */
class AqlGen
{
    const TYPE_FOR = 'FOR';
    const TYPE_LET = 'LET';
    const TYPE_COLLECT = 'COLLECT';
    const TYPE_FILTER = 'FILTER';
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    protected $for;
    protected $in;
    protected $inner = [];
    protected $sort = [];
    protected $skip;
    protected $limit;
    protected $return;
    protected $params = [];
    protected $requireReturn = true;

    /**
     * Build a FOR <var> IN <Expression>
     *
     * @param string $for alias to the collection or list <var>
     * @param string $inExpression collection name
     */
    public function __construct($for, $inExpression)
    {
        $this->for = $for;
        $this->in = $inExpression;
        return $this;
    }

    /**
     * Build a FOR <var> IN <Expression>
     *
     * @param string $for alias to the collection or list <var>
     * @param string $inExpression collection name
     */
    public static function query($for, $inExpression)
    {
        return new self($for, $inExpression);
    }

    /**
     * Add a subquery
     *
     * @param mixed|String|AqlGen $subquery
     * @return \AqlGen
     */
    public function subquery($subquery)
    {
        if ($subquery instanceof AqlGen) {
            $subquery->RequiredReturn(false);
        }
        $this->inner[] = [self::TYPE_FOR => $subquery];
        return $this;
    }

    /**
     * Add a LET expression
     *
     * @param String $var de variable name
     * @param mixed|string|AqlGen $expression
     * @return \AqlGen
     */
    public function let($var, $expression)
    {
        $this->inner[] = [self::TYPE_LET => [$var, $expression]];
        return $this;
    }

    /**
     * Add a COLLECT expression
     *
     * @param string $var
     * @param string $expression a atribute name
     * @param string $into variable name to group
     * @return \AqlGen
     */
    public function collect($var, $expression, $into = null)
    {
        $this->inner[] = [self::TYPE_COLLECT => [$var, $expression, $into]];
        return $this;
    }

    /**
     * Filter expression
     *
     * @param string $filterCriteria
     * @param Array $params the params that bind to filter
     *
     * eg 1 : $aql->filter('u.name == @name', ['name'=>'John']);
     * eg 2 : $aql->filter('u.name == @name && u.age == @age')->bindParams(['name'=>'John', 'age'=> 20]);
     *
     * @return \AqlGen
     */
    public function filter($filterCriteria, $params = [])
    {
        $this->addFilter($filterCriteria, $params, AqlFilter::AND_OPERATOR);
        return $this;
    }

    /**
     * Add filter with OR operator
     * @param string $filterCriteria
     * @param Array $params the params that bind to filter
     * @return \AqlGen
     */
    public function orFilter($filterCriteria, $params = [])
    {
        $this->addFilter($filterCriteria, $params, AqlFilter::OR_OPERATOR);
        return $this;
    }

    /**
     * Add SORT fields
     *
     * @param mixed|string|array $sort
     * @param string $direction
     */
    public function sort($sort, $direction = self::SORT_ASC)
    {
        if (is_array($sort)) {
            $sort = implode(', ', $sort);
        }
        $this->sort[] = $sort . ' ' . $direction;
    }

    public function skip($skip)
    {
        $this->skip = (int)$skip;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = (int)$limit;
        return $this;
    }

    /**
     * The mounted Aql query string
     * @return string
     */
    public function get()
    {
        $query = $this->getForString();
        $query .= $this->getInnerExpressionsString();
        $query .= $this->getSortString();
        $query .= $this->getLimitString();
        $query .= $this->getReturnString();
        return $query;
    }

    /**
     * Get expresions in order that are call
     *
     * @return string
     */
    protected function getInnerExpressionsString()
    {
        $query = '';
        foreach ($this->inner as $expressions) {
            foreach ($expressions as $type => $expression) {
                if (is_object($expression)) {
                    $expression = $expression->get();
                }

                switch ($type) {
                    case self::TYPE_FOR:
                        $type = null;
                        break;
                    case self::TYPE_LET:
                        if ($expression[1] instanceof AqlGen) {
                            $this->bindParams($expression[1]->getParams());
                            $expression[1] = '(' . $expression[1]->get() . ')';
                        }
                        $expression = ' ' . $expression[0] . ' = ' . $expression[1];
                        break;
                    case self::TYPE_COLLECT:
                        list($var, $value, $group) = $expression;
                        $expression = $var . ' = ' . $value;

                        if (!is_null($group)) {
                            $expression .= ' INTO ' . $group;
                        }
                        break;

                }

                $query .= "\t" . $type . ' ' . $expression . "\n";
            }
        }
        return $query;
    }

    /**
     * the IN part of query
     * @return type
     */
    protected function getForString()
    {
        return 'FOR ' . $this->for . ' IN ' . $this->in . " \n";
    }

    /**
     * the SORT part of query
     * @return string
     */
    protected function getSortString()
    {
        $query = '';
        if (!empty($this->sort)) {
            $sort = implode(', ', $this->sort);
            $query = "\tSORT " . $sort . "\n";
        }
        return $query;
    }

    /**
     * The LIMIT part of query
     * @return string
     */
    protected function getLimitString()
    {
        $str = '';
        if (!empty($this->limit)) {
            $str = "\t";
            if (!empty($this->skip)) {
                $str .= $this->skip . ' , ';
            }
            $str .= $this->limit . "\n";
        }
        return $str;
    }

    /**
     * the RETURN part of query
     * @return string
     */
    protected function getReturnString()
    {
        $query = '';
        if ($this->requireReturn) {
            if (is_null($this->return)) {
                $this->return = $this->for;
            }
        }

        if ($this->return !== null) {
            $query .= 'RETURN ' . $this->return;
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
     * Set a specific param to bind
     * @return string
     */
    public function bindParam($key, $value)
    {
        $this->params[$key] = $value;
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

    /**
     * set a RETURN part of query
     * @param type $return
     */
    public function setReturn($return)
    {
        $this->return = $return;
    }

    /**
     * Set if RETURN operator is required. Optional only in subqueries
     * @param boolean $isRequired
     */
    public function RequiredReturn($isRequired = true)
    {
        $this->requireReturn = $isRequired;
    }

    /**
     * Add filter item
     * @param String $filterCriteria
     * @param array $params
     * @param string $operator
     */
    protected function addFilter($filterCriteria, $params = [], $operator = AqlFilter::AND_OPERATOR)
    {
        $currentFilter = $this->getCurrentIndexFilter();
        $this->bindParams($params);
        if (!is_null($currentFilter)) {
            if ($operator == AqlFilter::AND_OPERATOR) {
                $currentFilter->andFilter($filterCriteria);
            } else {
                $currentFilter->orFilter($filterCriteria);
            }
            return;
        }

        $filterCriteria = new AqlFilter($filterCriteria);
        $this->inner[] = [self::TYPE_FILTER => $filterCriteria];
    }

    /**
     * Return the index of filter item if this is last inner item added
     * @return null|AqlFilter
     */
    protected function getCurrentIndexFilter()
    {
        if (!empty($this->inner)) {
            $filter = end($this->inner);
            $currentIndex = key($this->inner);
            if (key($filter) == self::TYPE_FILTER) {
                return $this->inner[$currentIndex][self::TYPE_FILTER];
            }
        }
        return null;
    }
}
