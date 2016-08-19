<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlRemove;

class AqlRemoveTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveExpressionByString()
    {
        $data = 'k1';

        $aql = new AqlRemove($data, 'users');
        $this->assertEquals('REMOVE k1 IN users ', $aql->get());
    }

    public function testRemoveWithOptions()
    {
        $data = 'k1';

        $aql = new AqlRemove($data, 'users');
        $aql->setOptions(array('waitForSync' => true));
        $this->assertEquals('REMOVE k1 IN users  OPTIONS {"waitForSync":true} ', $aql->get());
    }

    public function testRemoveExpressionByArray()
    {
        $data = array(
            '_key' => 'k1'
        );

        $aql = new AqlRemove($data, 'users');
        $this->assertEquals('REMOVE {_key:"k1"} IN users ', $aql->get());
    }
}
