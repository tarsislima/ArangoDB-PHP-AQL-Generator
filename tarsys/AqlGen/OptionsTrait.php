<?php
/**
 * Created by PhpStorm.
 * User: tarsis
 * Date: 19/08/16
 * Time: 14:01
 */

namespace tarsys\AqlGen;


trait OptionsTrait
{
    protected $options;

    public function setOptions($options = null)
    {
        if (isset($options)) {
            $this->options = " OPTIONS {$this->normalizeDocument($options)} ";
        }
        return;
    }
}