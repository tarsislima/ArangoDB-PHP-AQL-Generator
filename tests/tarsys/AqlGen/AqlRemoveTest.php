<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlRemove;
use tarsys\AqlGen\AqlReplace;

class AqlRemoveTest extends PHPUnit_Framework_TestCase
{

    public function testRemoveExpressionByString()
    {
        $data = 'Gilbert';

        $aql = new AqlRemove($data, 'users');
        $this->assertEquals('REMOVE "Gilbert" IN users ', $aql->get());
    }

    public function testRemoveExpressionByArray()
    {
        $data = array(
            '_key' => 'Gilbert'
        );

        $aql = new AqlRemove($data, 'users');
        $this->assertEquals('REMOVE {_key:"Gilbert"} IN users ', $aql->get());
    }
}
