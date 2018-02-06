<?php

namespace Concrete\Tests\Database\Driver;

use Concrete\Core\Database\Driver\DriverManager;
use PHPUnit_Framework_TestCase;

class DriverManagerTest extends PHPUnit_Framework_TestCase
{
    /** @var DriverManager */
    protected $driverManager;

    public function setUp()
    {
        $this->driverManager = new DriverManager(\Core::getFacadeRoot());
    }

    public function testConfigLoad()
    {
        $this->driverManager->configExtensions(
            [
                'test' => __CLASS__,
            ]
        );

        $this->assertInstanceOf(__CLASS__, $this->driverManager->driver('test'));
    }
}
