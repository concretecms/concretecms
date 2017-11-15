<?php

class IPAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Utility\IPAddress
     */
    protected $object;

    protected $fixtures = array();
    protected $tables = array();

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
        return array(
            array('127.0.0.0', true),
            array('127.0.0.1', true),
            array('127.255.255.255', true),
            array('192.168.0.1', false),
            array('::1', true),
            array('2001:0db8:85a3:0000:0000:8a2e:0370:7334', false),
            array('::ffff:127.0.0.1', true),
        );
    }

    /**
     * @dataProvider loopbackDataProvider
     */
    public function testLoopback($ip, $expected)
    {
        $this->assertEquals($expected, $this->object->setIp($ip)->isLoopback());
    }

    public function privateDataProvider()
    {
        return array(
            array('192.168.0.1', true),
            array('172.16.0.1', true),
            array('172.33.0.1', false),
            array('10.1.0.1', true),
            array('127.0.0.1', false),
            array('127.255.255.255', false),
            array('8.8.8.8', false),

            array('fc00::1', true),
            array('fcff::1', true),
            array('fd00:ffff:ffff:ffff:ffff:ffff:ffff:ffff', true),
            array('::1', false),
            array('2001:0db8:85a3:0000:0000:8a2e:0370:7334', false),
            array('::ffff:127.0.0.1', false),
            array('::ffff:192.168.0.1', true),
            array('::ffff:172.16.0.1', true),
            array('::ffff:10.1.0.1', true),
        );
    }

    /**
     * @dataProvider privateDataProvider
     */
    public function testPrivate($ip, $expected)
    {
        $this->assertEquals($expected, $this->object->setIp($ip)->isPrivate());
    }

    public function linkedLocalDataProvider()
    {
        return array(
            array('169.254.255.255', true),
            array('169.255.0.0', false),
            array('169.253.255.255', false),

            array('fe80::1', true),
            array('fe90::1', true),
            array('fea0::1', true),
            array('feb0::1', true),
            array('fe7f:ffff:ffff:ffff:ffff:ffff:ffff:ffff', false),
            array('fec0::1', false),
            array('::ffff:169.254.255.255', true),
            array('::ffff:169.255.0.0', false),
            array('::ffff:169.253.255.255', false),
        );
    }

    /**
     * @dataProvider linkedLocalDataProvider
     */
    public function testLinkedLocal($ip, $expected)
    {
        $this->assertEquals($expected, $this->object->setIp($ip)->isLinkLocal());
    }

    public function ipTypeDataProvider()
    {
        return array(
            array('169.254.255.255', 4),
            array('169.254.255.255:2222', 4),
            array('127.0.0.1', 4),
            array('10.255.255.255', 4),

            array('fe80::1', 6),
            array('[fe80::1]:2222', 6),
            array('::1', 6),
            array('fe7f:ffff:ffff:ffff:ffff:ffff:ffff:ffff', 6),
            array('::ffff:169.254.255.255', 6),
            array('::ffff:127.0.0.1', 6),
            array('::ffff:10.255.255.255', 6),
            array('::ffff:10.255.255.255:2222', 6),
            array('[::ffff:10.255.255.255]:2222', 6),
        );
    }
    /**
     * @dataProvider ipTypeDataProvider
     */
    public function testIpType($ip, $expected)
    {
        $this->object->setIp($ip);
        $this->assertEquals($expected, ($this->object->isIPv4()) ? (4) : ($this->object->isIPv6() ? 6 : 0));
    }
}
