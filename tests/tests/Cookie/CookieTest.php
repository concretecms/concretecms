<?php

namespace Concrete\Tests\Error;

use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Http\Request;
use PHPUnit_Framework_TestCase;

class CookieTest extends PHPUnit_Framework_TestCase
{
    public function testCookiesFromRequest()
    {
        $jar = new CookieJar($this->getSampleRequest([]));
        $this->assertFalse($jar->has('test'));
        $jar = new CookieJar($this->getSampleRequest(['test' => 'RequestCookieValue']));
        $this->assertTrue($jar->has('test'));
        $this->assertSame('RequestCookieValue', $jar->get('test'));
    }

    public function testOverridingCookies()
    {
        $jar = new CookieJar($this->getSampleRequest(['test1' => 'RequestCookieValue1', 'test2' => 'RequestCookieValue2']));

        $this->assertTrue($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test1' => 'RequestCookieValue1', 'test2' => 'RequestCookieValue2'], $jar->getAllCookies());
        $this->assertSame([], $jar->getClearedCookies());
        $this->assertCount(0, $jar->getNewCookies());

        $jar->clear('test1');
        $this->assertFalse($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test2' => 'RequestCookieValue2'], $jar->getAllCookies());
        $this->assertSame(['test1'], $jar->getClearedCookies());
        $this->assertCount(0, $jar->getNewCookies());

        $jar->set('test1', 'NewCookieValue1');
        $this->assertTrue($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test1' => 'NewCookieValue1', 'test2' => 'RequestCookieValue2'], $jar->getAllCookies());
        $this->assertSame([], $jar->getClearedCookies());
        $this->assertCount(1, $jar->getNewCookies());

        $jar->set('test3', 'NewCookieValue3');
        $this->assertTrue($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertTrue($jar->has('test3'));
        $this->assertSame(['test1' => 'NewCookieValue1', 'test2' => 'RequestCookieValue2', 'test3' => 'NewCookieValue3'], $jar->getAllCookies());
        $this->assertSame([], $jar->getClearedCookies());
        $this->assertCount(2, $jar->getNewCookies());

        $jar->clear('test1');
        $jar->clear('test4');
        $this->assertFalse($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test2' => 'RequestCookieValue2', 'test3' => 'NewCookieValue3'], $jar->getAllCookies());
        $this->assertSame(['test1', 'test4'], $jar->getClearedCookies());
        $this->assertCount(1, $jar->getNewCookies());
    }

    /**
     * @param array $cookies
     *
     * @return \Concrete\Core\Http\Request
     */
    protected function getSampleRequest(array $cookies)
    {
        return Request::create('https://www.example.com/', 'GET', [], $cookies);
    }
}
