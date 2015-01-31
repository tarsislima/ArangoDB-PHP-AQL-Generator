<?php
/**
 * Created by PhpStorm.
 * User: tarsis
 * Date: 10/24/14
 * Time: 1:11 PM
 */

namespace tarsys\AqlGen;

abstract class AbstractAql
{
    //const LINE_SEPARATOR = "\n";
    const TAB_SEPARATOR = "\t";

    protected $params = array();

    abstract public function get();

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
     * for to reset params before performs get() method in 'inner' queries
     */
    protected function resetParams()
    {
        $this->params = array();
    }
}
