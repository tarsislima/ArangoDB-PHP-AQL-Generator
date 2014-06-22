<?php

/**
 * 
 */
class Aql
{

    const TYPE_FOR = 'FOR';
    const TYPE_LET = 'LET';
    const TYPE_COLLECT = 'COLLECT';
    const TYPE_FILTER = 'FILTER';

    public $for;
    public $in;
    //
    public $inner = [];
    public $sort;
    public $limit;
    public $return;
    public $params = [];
    public $requireReturn = true;

    /**
     * Build a FOR <var> IN <Expression> 
     * 
     * @param string $for alias to the collection or list <var> 
     * @param mixed|String|Array $inExpression collection name or list
     * 
     * if pass an array result in a list like eg: IN ["key1':val1, "key2":val2]
     */
    public function query($for, $inExpression)
    {
        $this->for = $for;
        $this->in = $inExpression;
        return $this;
    }

    /**
     * Add a subquery 
     * 
     * @param mixed|String|Aql $subquery
     * @return \Aql
     */
    public function subquery($subquery)
    {
        if ($subquery instanceof Aql) {
            $subquery->requireReturn = false;
        }
        $this->inner[] = [self::TYPE_FOR => $subquery];
        return $this;
    }

    /**
     * Add a Let expression
     * 
     * @param String $var de variable name
     * @param mixed|string|Aql $expression 
     * @return \Aql
     */
    public function let($var, $expression)
    {
        $this->inner[] = [self::TYPE_LET => [$var, $expression]];
        return $this;
    }

    /**
     * Add a Collect expression
     * 
     * @param string $var
     * @param string $expression a atribute name
     * @param string $into variable name to group
     * @return \Aql
     */
    public function collect($var, $expression, $into = null)
    {
        $this->inner[] = [self::TYPE_COLLECT => [$var, $expression, $into]];
        return $this;
    }
    
    /**
     * Filter query 
     * 
     * @param mixed|String|Filter  $filterCriteria
     * @param Array $params the params that bind to filter 
     * 
     * eg 1 : $aql->filter('u.name == @name', ['name'=>'John']);
     * eg 2 : $aql->filter('u.name == @name')->setParams(['name'=>'John']);
     * eg 3 : $filter  = new Filter('u.name == @name', ['name'=>'John']);
     *        
     *         $aql->filter($filter);
     * 
     * @return \Aql
     */
    public function filter($filterCriteria, $params = [])
    {
        $this->setParams($params);
        $this->inner[] = [self::TYPE_FILTER => $filterCriteria];
        return $this;
    }

    /**
     * The mounted Aql query string 
     * @return string
     */
    public function get()
    {
        $query = 'FOR ' . $this->for . $this->getInString();

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
                    $this->setParams($expression->getParams());
                    $expression = $expression->get();
                }

                switch ($type) {
                    case self::TYPE_FOR:
                        $type = null;
                        break;
                    case self::TYPE_LET:
                        if ($expression[1] instanceof Aql) {
                            $this->setParams($expression[1]->getParams());
                            $expression[1] = '(' . $expression[1]->get() . ')';
                        }
                        $expression = ' ' . $expression[0] . ' = ' . $expression[1];
                        break;
                    case self::TYPE_COLLECT:
                        $expression = $expression[0] . ' = ' . $expression[1];
                        if (!is_null($expression[2])) {
                            $expression .=' INTO ' . $expression[2];
                        }
                        break;
                    case self::TYPE_FILTER:
                        break;
                }

                $query .= $type . ' ' . $expression . "\n";
            }
        }
        return $query;
    }

    /**
     * the IN part of query
     * @return type
     */
    protected function getInString()
    {
        if (!is_string($this->in)) {
            if (is_array($this->in)) {
                $this->in = '[' . $this->arrayToList($this->in) . ']';
            }
        }
        return ' IN ' . $this->in . " \n";
    }

    /**
     * to implement 
     * @return type
     */
    protected function getSortString()
    {
        $query = '';
        if ($this->sort !== null) {
            $sort = $this->sort;

            if (is_array($this->sort)) {
                //todo
            }
            $query = 'SORT ' . $sort . "\n";
        }
        return $query;
    }

    /**
     * the LIMIT part of query
     * @return string
     */
    protected function getLimitString()
    {
        return $this->limit . "\n";
        ;
    }

    /**
     * the Return part of query
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
            if (is_array($this->return)) {
                $this->return = '{' . $this->arrayToList($this->return) . '}';
            }
            $query .= 'RETURN ' . $this->return;
        }
        return $query;
    }

    /**
     * Set params to bind
     * @param Array $params Key => values of variables to bind 
     * @return string
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Set a param to bind
     * @return string
     */
    public function addParam($key, $value)
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
     * Convert array format to key value with colon separator 
     * 
     * @param array $array
     * @return string
     */
    public function arrayToList(Array $array)
    {
        array_walk($array, function (&$list, $key) {
                    $list = '"' . $key . '" : ' . $list;
                });
        $listString = implode(', ', $array);
        return $listString;
    }
}
