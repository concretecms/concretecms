<?php

namespace Concrete\Tests\Cookie;

use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Cookie\ResponseCookieJar;
use Concrete\Core\Http\Request;
use Concrete\Tests\TestCase;

class CookieTest extends TestCase
{
    public function testCookiesFromRequest()
    {
        $request = $this->getSampleRequest([]);
        $jar = new CookieJar($request, new ResponseCookieJar($request));
        $this->assertFalse($jar->has('test'));
        $request = $this->getSampleRequest(['test' => 'RequestCookieValue']);
        $jar = new CookieJar($request, new ResponseCookieJar($request));
        $this->assertTrue($jar->has('test'));
        $this->assertSame('RequestCookieValue', $jar->get('test'));
    }

    public function testOverridingCookies()
    {
        $request = $this->getSampleRequest(['test1' => 'RequestCookieValue1', 'test2' => 'RequestCookieValue2']);
        $jar = new CookieJar($request, new ResponseCookieJar($request));

        $this->assertTrue($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test1' => 'RequestCookieValue1', 'test2' => 'RequestCookieValue2'], $jar->getAll());
        $this->assertSame([], $jar->getResponseCookies()->getClearedCookies());
        $this->assertCount(0, $jar->getResponseCookies()->getCookies());

        $jar->clear('test1');
        $this->assertFalse($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test2' => 'RequestCookieValue2'], $jar->getAll());
        $this->assertSame(['test1'], $jar->getResponseCookies()->getClearedCookies());
        $this->assertCount(0, $jar->getResponseCookies()->getCookies());

        $jar->set('test1', 'NewCookieValue1');
        $this->assertTrue($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test1' => 'NewCookieValue1', 'test2' => 'RequestCookieValue2'], $jar->getAll());
        $this->assertSame([], $jar->getResponseCookies()->getClearedCookies());
        $this->assertCount(1, $jar->getResponseCookies()->getCookies());

        $jar->set('test3', 'NewCookieValue3');
        $this->assertTrue($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertTrue($jar->has('test3'));
        $this->assertSame(['test1' => 'NewCookieValue1', 'test2' => 'RequestCookieValue2', 'test3' => 'NewCookieValue3'], $jar->getAll());
        $this->assertSame([], $jar->getResponseCookies()->getClearedCookies());
        $this->assertCount(2, $jar->getResponseCookies()->getCookies());

        $jar->clear('test1');
        $jar->clear('test4');
        $this->assertFalse($jar->has('test1'));
        $this->assertTrue($jar->has('test2'));
        $this->assertSame(['test2' => 'RequestCookieValue2', 'test3' => 'NewCookieValue3'], $jar->getAll());
        $this->assertSame(['test1', 'test4'], $jar->getResponseCookies()->getClearedCookies());
        $this->assertCount(1, $jar->getResponseCookies()->getCookies());
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
