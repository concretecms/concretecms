<?php

require_once __DIR__ . '/ExpressEntityManagerTestCaseTrait.php';

class ObjectManagerTest extends PHPUnit_Framework_TestCase
{

    use ExpressEntityManagerTestCaseTrait;

    public function test()
    {
        $em = $this->getMockEntityManager();
        $express = Core::make('express');
        $student = $express->create('Student');
    }

}
