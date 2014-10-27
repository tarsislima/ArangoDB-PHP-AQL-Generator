<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlGen;

/**
 * Class to build AQL strings
 *
 * @author Társis Lima
 */
class AqlGenTest extends PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        $aql = AqlGen::query('u', 'users');
        $this->assertTrue($aql instanceof AqlGen);
        $string = $aql->get();

        $this->assertEquals("FOR u IN users\nRETURN u", $string);
    }

    /*
       public function testReturn()
       {
           $aql = AqlGen::query('u', 'users')->setReturn("{'name': u.name}");
           $string = $aql->get();
           $this->assertEquals("FOR u IN users \nRETURN {'name': u.name}", $string);
       }

       public function testSort()
       {
           $aql = AqlGen::query('u', 'users')->sort('u.name');
           $string = $aql->get();
           $this->assertEquals("FOR u IN users \n\tSORT u.name ASC\nRETURN u", $string);

           $aql = AqlGen::query('u', 'users')->sort('u.name', AqlGen::SORT_DESC);
           $string = $aql->get();
           $this->assertEquals("FOR u IN users \n\tSORT u.name DESC\nRETURN u", $string);

           $aql = AqlGen::query('u', 'users')->sort(array('u.name', 'u.points'), AqlGen::SORT_ASC)
               ->sort(array('u.age'), AqlGen::SORT_DESC);

           $string = $aql->get();
           $this->assertEquals("FOR u IN users \n\tSORT u.name, u.points ASC, u.age DESC\nRETURN u", $string);
       }

       public function testLimit()
       {
           $aql = AqlGen::query('u', 'users')->limit(10);
           $string = $aql->get();
           $this->assertEquals("FOR u IN users \n\tLIMIT 10\nRETURN u", $string);

           $aql = AqlGen::query('u', 'users')->skip(2);
           $string = $aql->get();
           $this->assertEquals("FOR u IN users \nRETURN u", $string);

           $aql = AqlGen::query('u', 'users')->limit(10);
           $string = $aql->get();
           $this->assertEquals("FOR u IN users \n\tLIMIT 10\nRETURN u", $string);
       }*/

    public function testSubqueryNotHaveReturn()
    {
        $friendsQuery = AqlGen::query('f', 'friends');

        $aql = AqlGen::query('u', 'users')
            ->subquery($friendsQuery);

        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFOR f IN friends\nRETURN u", $string);
        return;
        $friendsQuery->setReturn('f');
        $aql = AqlGen::query('u', 'users')
            ->subquery($friendsQuery);

        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFOR f IN friends\nRETURN u", $string);
    }

    /*    public function subquery(AqlGen $subquery)
        {
            $subquery->setSubquery();
            $this->bindParams($subquery->getParams());
            $this->inner[] = [self::TYPE_FOR => $subquery];
            return $this;
        }

        public function let($var, $expression)
        {
            if ($expression instanceof AqlGen) {
                $this->bindParams($expression->getParams());
            }
            $this->inner[] = [self::TYPE_LET => [$var, $expression]];
            return $this;
        }

        public function collect($var, $expression, $into = null)
        {
            $this->inner[] = [self::TYPE_COLLECT => [$var, $expression, $into]];
            return $this;
        }


        public function filter($filterCriteria, $params = [])
        {
            $this->addFilter($filterCriteria, $params, AqlFilter::AND_OPERATOR);
            return $this;
        }
        public function orFilter($filterCriteria, $params = [])
        {
            $this->addFilter($filterCriteria, $params, AqlFilter::OR_OPERATOR);
            return $this;
        }

        public function get()
        {
            $query = $this->getForString();
            $query .= $this->getInnerExpressionsString();
            $query .= $this->getSortString();
            $query .= $this->getLimitString();
            $query .= $this->getReturnString();
            return $query;
        }
        protected function getInnerExpressionsString()
        {
            $query = '';
            foreach ($this->inner as $expressions) {
                foreach ($expressions as $type => $expression) {

                    switch ($type) {
                        case self::TYPE_FOR:
                            $type = null;
                            $expression = $expression->get();
                            break;

                        case self::TYPE_FILTER:
                            $expression = $expression->get();
                            break;

                        case self::TYPE_LET:
                            if ($expression[1] instanceof AqlGen) {
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

                    $query .= self::TAB_SEPARATOR . $type . ' ' . $expression . self::LINE_SEPARATOR;
                }
            }
            return $query;
        }

        protected function getForString()
        {
            return "FOR {$this->for} IN {$this->in} " . self::LINE_SEPARATOR;
        }

        protected function getSortString()
        {
            $query = '';
            if (!empty($this->sort)) {
                $sort = implode(', ', $this->sort);
                $query = self::TAB_SEPARATOR . "SORT " . $sort . self::LINE_SEPARATOR;
            }
            return $query;
        }


        protected function getLimitString()
        {
            $str = '';
            if (!empty($this->limit)) {
                $str = self::TAB_SEPARATOR;
                if (!empty($this->skip)) {
                    $str .= $this->skip . ' , ';
                }
                $str .= 'LIMIT ' . $this->limit . self::LINE_SEPARATOR;
            }
            return $str;
        }

        protected function getReturnString()
        {
            if (!$this->isSubQuery) {
                if (is_null($this->return)) {
                    $this->return = new AqlReturn($this->for);
                }
            }

            return $this->return->get();
        }


        public function bindParams($params)
        {
            if (!empty($params)) {
                $this->params = array_merge($this->params, $params);
            }
            return $this;
        }

        public function bindParam($key, $value)
        {
            $this->params[$key] = $value;
            return $this;
        }


        public function getParams()
        {
            return $this->params;
        }

        public function setReturn($return)
        {
            $this->return = new AqlReturn($return);
            return $this;
        }

        public function insert($document = null, $collection = null)
        {
            if (is_null($document)) {
                $document = $this->for;
            }
            if (is_null($collection)) {
                $collection = $this->in;
            }
            $this->return = new AqlInsert($document, $collection);
            $this->checkOperationReturn();
            return $this;
        }


        public function update($data, $document = null, $collection = null)
        {
            if (is_null($document)) {
                $document = $this->for;
            }
            if (is_null($collection)) {
                $collection = $this->in;
            }
            $this->return = new AqlUpdate($document, $collection, $data);
            $this->checkOperationReturn();
            return $this;
        }

        public function replace($document = null, $collection = null)
        {
            $this->return = new AqlReplace($document, $collection);
            $this->checkOperationReturn();
            return $this;
        }

        public function delete($document = null, $collection = null)
        {
            $this->operation = self::OPERATION_DELETE;
            return $this->setCollectionOperation($document, $collection);
        }
        protected function setCollectionOperation($document = null, $collection = null, $with = null)
        {
            if (is_null($document)) {
                $document = $this->for;
            }
            if (is_null($collection)) {
                $collection = $this->in;
            }
            $return = $document . " {$with} IN " . $collection;
            return $this->setOperationReturn($return);
        }

        private function checkOperationReturn()
        {
            if ($this->isSubQuery == true && !$this->return instanceof AqlReturn) {
                throw new InvalidArgumentException("A subquery not should have a {$this->operation} expression.");
            }
            return $this;
        }
        public function setSubquery()
        {
            $this->isSubQuery = true;
        }

        protected function addFilter($filterCriteria, $params = [], $operator = AqlFilter::AND_OPERATOR)
        {
            if (!$filterCriteria instanceof AqlFilter) {
                $filterCriteria = new AqlFilter($filterCriteria);
                if (!empty($params)) {
                    $filterCriteria->bindParams($params);
                }
            }

            $currentFilter = $this->getCurrentIndexFilter();
            $this->bindParams($filterCriteria->getParams());

            if (!is_null($currentFilter)) {
                $criteria = $filterCriteria->get();
                if ($operator == AqlFilter::AND_OPERATOR) {
                    $currentFilter->andFilter($criteria);
                } else {
                    $currentFilter->orFilter($criteria);
                }
                return;
            }

            $this->inner[] = [self::TYPE_FILTER => $filterCriteria];
        }

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

        public function __toString()
        {
            return $this->get();
        }*/
}
