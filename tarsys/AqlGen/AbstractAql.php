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
    const LINE_SEPARATOR = "\n";
    const TAB_SEPARATOR = "\t";

    abstract public function get();
}
