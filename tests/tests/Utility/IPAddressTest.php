<?php

namespace Concrete\Tests\Utility;

use PHPUnit_Framework_TestCase;

class IPAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Utility\IPAddress
     */
    protected $object;

    protected $fixtures = [];
    protected $tables = [];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Concrete\Core\Utility\IPAddress();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
        parent::tearDown();
    }

    public function loopbackDataProvider()
    {
        return [
            ['127.0.0.0', true],
            ['127.0.0.1', true],
            ['127.255.255.255', true],
            ['192.168.0.1', false],
            ['::1', true],
            ['2001:0db8:85a3:0000:0000:8a2e:0370:7334', false],
            ['::ffff:127.0.0.1', true],
        ];
    }

    /**
     * @dataProvider loopbackDataProvider
     *
     * @param mixed $ip
     * @param mixed $expected
     */
    public function testLoopback($ip, $expected)
    {
        $this->assertEquals($expected, $this->object->setIp($ip)->isLoopback());
    }

    public function privateDataProvider()
    {
        return [
            ['192.168.0.1', true],
            ['172.16.0.1', true],
            ['172.33.0.1', false],
            ['10.1.0.1', true],
            ['127.0.0.1', false],
            ['127.255.255.255', false],
            ['8.8.8.8', false],

            ['fc00::1', true],
            ['fcff::1', true],
            ['fd00:ffff:ffff:ffff:ffff:ffff:ffff:ffff', true],
            ['::1', false],
            ['2001:0db8:85a3:0000:0000:8a2e:0370:7334', false],
            ['::ffff:127.0.0.1', false],
            ['::ffff:192.168.0.1', true],
            ['::ffff:172.16.0.1', true],
            ['::ffff:10.1.0.1', true],
        ];
    }

    /**
     * @dataProvider privateDataProvider
     *
     * @param mixed $ip
     * @param mixed $expected
     */
    public function testPrivate($ip, $expected)
    {
        $this->assertEquals($expected, $this->object->setIp($ip)->isPrivate());
    }

    public function linkedLocalDataProvider()
    {
        return [
            ['169.254.255.255', true],
            ['169.255.0.0', false],
            ['169.253.255.255', false],

            ['fe80::1', true],
            ['fe90::1', true],
            ['fea0::1', true],
            ['feb0::1', true],
            ['fe7f:ffff:ffff:ffff:ffff:ffff:ffff:ffff', false],
            ['fec0::1', false],
            ['::ffff:169.254.255.255', true],
            ['::ffff:169.255.0.0', false],
            ['::ffff:169.253.255.255', false],
        ];
    }

    /**
     * @dataProvider linkedLocalDataProvider
     *
     * @param mixed $ip
     * @param mixed $expected
     */
    public function testLinkedLocal($ip, $expected)
    {
        $this->assertEquals($expected, $this->object->setIp($ip)->isLinkLocal());
    }

    public function ipTypeDataProvider()
    {
        return [
            ['169.254.255.255', 4],
            ['169.254.255.255:2222', 4],
            ['127.0.0.1', 4],
            ['10.255.255.255', 4],

            ['fe80::1', 6],
            ['[fe80::1]:2222', 6],
            ['::1', 6],
            ['fe7f:ffff:ffff:ffff:ffff:ffff:ffff:ffff', 6],
            ['::ffff:169.254.255.255', 6],
            ['::ffff:127.0.0.1', 6],
            ['::ffff:10.255.255.255', 6],
            ['::ffff:10.255.255.255:2222', 6],
            ['[::ffff:10.255.255.255]:2222', 6],
        ];
    }

    /**
     * @dataProvider ipTypeDataProvider
     *
     * @param mixed $ip
     * @param mixed $expected
     */
    public function testIpType($ip, $expected)
    {
        $this->object->setIp($ip);
        $this->assertEquals($expected, ($this->object->isIPv4()) ? (4) : ($this->object->isIPv6() ? 6 : 0));
    }
}
