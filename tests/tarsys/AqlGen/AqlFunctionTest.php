<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlFunction;
use tarsys\AqlGen\AqlGen;

class AqlFunctionTest extends PHPUnit_Framework_TestCase
{
    public function testNoArgumentsThrowException()
    {
        try {
            new AqlFunction('FIRST', array());
            $this->fail('expected exception');
        } catch (\Exception $e) {
        }

        try {
            new AqlFunction('FIRST', 'a');
            $this->fail('expected exception');
        } catch (\Exception $e) {
        }

        try {
            new AqlFunction('FIRST', ['a']);
            $this->fail('expected exception');
        } catch (\Exception $e) {
        }
    }

    public function testReturnExpression()
    {
        $aql = AqlGen::query('u', 'users');
        $firstFunction = new AqlFunction('FIRST', ['a', 'b']);
        $this->assertEquals('FIRST (a, b)', $firstFunction->get());
        $firstFunction = new AqlFunction('FIRST', array($aql));
        $this->assertEquals("FIRST ((FOR u IN users\nRETURN u))", $firstFunction->get());
    }

}
