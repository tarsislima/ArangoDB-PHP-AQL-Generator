<?php

namespace tests\tarsys;

use PHPUnit_Framework_TestCase;
use tarsys\AqlGen\AqlGen;
use tarsys\AqlGen\AqlFilter;

/**
 * Class to build AQL strings
 *
 * @author Társis Lima
 */
class AqlGenTest extends PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        $aql = AqlGen::query('u', 'users');
        $this->assertTrue($aql instanceof AqlGen);
        $string = $aql->get();

        $this->assertEquals("FOR u IN users\nRETURN u", $string);
    }

    public function testQueryWithArrayInCollection()
    {
        //need implementation
    }

    public function testReturn()
    {
        $aql = AqlGen::query('u', 'users')->setReturn("{'name': u.name}");
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\nRETURN {'name': u.name}", $string);
    }

    public function testSort()
    {
        $aql = AqlGen::query('u', 'users')->sort('u.name');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tSORT u.name ASC\nRETURN u", $string);

        $aql = AqlGen::query('u', 'users')->sort('u.name', AqlGen::SORT_DESC);
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tSORT u.name DESC\nRETURN u", $string);

        $aql = AqlGen::query('u', 'users')->sort(array('u.name', 'u.points'), AqlGen::SORT_ASC)
            ->sort(array('u.age'), AqlGen::SORT_DESC);

        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tSORT u.name, u.points ASC, u.age DESC\nRETURN u", $string);
    }

    public function testLimit()
    {
        $aql = AqlGen::query('u', 'users')->limit(10);
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tLIMIT 10\nRETURN u", $string);
    }

    public function testLimitWithOfset()
    {
        $aql = AqlGen::query('u', 'users')->skip(2);
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\nRETURN u", $string);

        $aql = AqlGen::query('u', 'users')->limit(10)->skip(2);
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tLIMIT 2, 10\nRETURN u", $string);
    }

    public function testSubqueryNotHaveReturn()
    {
        $friendsQuery = AqlGen::query('f', 'friends');

        $aql = AqlGen::query('u', 'users')
            ->subquery($friendsQuery);

        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFOR f IN friends\nRETURN u", $string);
    }

    public function testSubqueryWithReturnThrowError()
    {
        $friendsQuery = AqlGen::query('f', 'friends');
        $friendsQuery->setReturn('f');
        $aql = AqlGen::query('u', 'users')
            ->subquery($friendsQuery);

        $this->setExpectedException('InvalidArgumentException', "A subquery not should have a RETURN operation.");
        $aql->get();
    }

    public function testLetWithVar()
    {
        $aql = AqlGen::query('u', 'users')->let('points', '10');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tLET points = 10\nRETURN u", $string);
    }

    public function testLetWithSubquery()
    {
        $friendsQuery = AqlGen::query('f', 'friends');
        $aql = AqlGen::query('u', 'users')->let('points', $friendsQuery);
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tLET points = (FOR f IN friends\nRETURN f)\nRETURN u", $string);
    }

    public function testCollect()
    {
        $aql = AqlGen::query('u', 'users')->collect('points', 'u.name');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tCOLLECT points = u.name\nRETURN u", $string);

        $aql = AqlGen::query('u', 'users')->collect('points', 'u.name', 'group');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tCOLLECT points = u.name INTO group\nRETURN u", $string);
    }

    public function testAndFilter()
    {
        $aql = AqlGen::query('u', 'users')->filter('u.active = true');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFILTER u.active = true\nRETURN u", $string);

        $aql = AqlGen::query('u', 'users')->filter('u.active = true')->filter('u.age > 20');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFILTER u.active = true && u.age > 20\nRETURN u", $string);
    }

    public function testAndFilterWithParams()
    {
        $aql = AqlGen::query('u', 'users')->filter('u.age = @age', ['@age' => 20]);
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFILTER u.age = @age\nRETURN u", $string);
        $params = $aql->getParams();
        $this->assertArrayHasKey('@age', $params);
        $this->assertEquals($params['@age'], 20);
    }

    public function testOrFilter()
    {
        $aql = AqlGen::query('u', 'users')->filter('u.active = true')->orFilter('u.age > 20');
        $string = $aql->get();
        $this->assertEquals("FOR u IN users\n\tFILTER u.active = true || u.age > 20\nRETURN u", $string);
    }

    public function testObjectFilter()
    {
        $filter = new AqlFilter();
        $filter->andFilter('u.active = true');
        $filter->andFilter('u.age > @minAge');
        $filter->andFilter('u.age < @maxAge');
        $filter->bindParams(
            [
                '@minAge' => 20,
                '@maxAge' => 50,
            ]
        );

        $aql = AqlGen::query('u', 'users')->filter($filter);
        $string = $aql->get();
        $this->assertEquals(
            "FOR u IN users\n\tFILTER u.active = true && u.age > @minAge && u.age < @maxAge\nRETURN u",
            $string
        );

        $params = $aql->getParams();
        $this->assertArrayHasKey('@minAge', $params);
        $this->assertEquals($params['@minAge'], 20);

        $this->assertArrayHasKey('@maxAge', $params);
        $this->assertEquals($params['@maxAge'], 50);
    }
}
