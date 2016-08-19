<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlGen;
use tarsys\AqlGen\AqlUpdate;
use tarsys\AqlGen\AqlExpr;

class AqlUpdateTest extends PHPUnit_Framework_TestCase
{
    public function testUpdateDocumentSintax()
    {
        $document = array(
            'status' => 'active',
            'location' => 'Beijing'
        );

        $insert = new AqlUpdate("u", $document, 'users');
        $this->assertEquals('UPDATE u WITH {"status":"active","location":"Beijing"} IN users ', $insert->get());
    }

    public function testUpdateKeyExpressionSintax()
    {
        $data = array(
            'status' => 'active',
            'location' => 'Beijing'
        );

        $insert = new AqlUpdate(array('_key' => "u._key"), $data, 'users');
        $this->assertEquals('UPDATE {_key:"u._key"} WITH {"status":"active","location":"Beijing"} IN users ', $insert->get());

        $document = array('_key' => AqlGen::expr("CONCAT('TEST', i)"));
        $insert = new AqlUpdate($document, $data, 'users');
        $this->assertEquals('UPDATE {_key:CONCAT(\'TEST\', i)} WITH {"status":"active","location":"Beijing"} IN users ', $insert->get());
    }

    public function testUpdateWithOptions()
    {
        $data = array(
            'status' => 'active',
            'location' => 'Beijing'
        );

        $aql = new AqlUpdate('u._key', $data, 'users');
        $aql->setOptions(['waitForSync' => true]);
        $this->assertEquals('UPDATE u._key WITH {"status":"active","location":"Beijing"} IN users  OPTIONS {"waitForSync":true} ', $aql->get());

    }
}
