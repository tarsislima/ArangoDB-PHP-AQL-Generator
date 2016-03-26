<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlReplace;

class AqlReplaceTest extends PHPUnit_Framework_TestCase
{

    public function testReplaceExpression()
    {
        $data = array(
            'name' => 'Paul'
        );

        $insert = new AqlReplace($data, 'users');
        $this->assertEquals('REPLACE {"name":"Paul"} IN users ', $insert->get());
    }

    public function testReplaceExpressionWithOptions()
    {
        $data = array(
            'name' => 'Paul'
        );

        $options = array("ignoreErrors" => true);

        $insert = new AqlReplace($data, 'users', $options);
        $this->assertEquals('REPLACE {"name":"Paul"} IN users OPTIONS {"ignoreErrors":true} ', $insert->get());
    }
}
