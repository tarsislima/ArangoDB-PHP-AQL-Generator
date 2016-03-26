<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlReplace;
use tarsys\AqlGen\AqlUpdate;

class AqlUpdateTest extends PHPUnit_Framework_TestCase
{
    public function testUpdateExpression()
    {
        $data = array(
            'status' => 'active',
            'location' => 'Beijing'
        );

        $insert = new AqlUpdate("PhilCarpenter", $data, 'users');
        $this->assertEquals('UPDATE "PhilCarpenter" WITH {"status":"active","location":"Beijing"} IN users ', $insert->get());
    }
}
