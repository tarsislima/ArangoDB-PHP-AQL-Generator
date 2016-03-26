<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlInsert;

class AqlInsertTest extends PHPUnit_Framework_TestCase
{

    public function testInsertExpression()
    {
        $data = array(
            'name' => 'Jhon',
            'age' => 18,
            'tags' => array(
                'music',
                'sports',
                'dance'
            )
        );

        $insert = new AqlInsert($data, 'users');
        $this->assertEquals('INSERT {"name":"Jhon","age":18,"tags":["music","sports","dance"]} IN users ', $insert->get());
    }
}
