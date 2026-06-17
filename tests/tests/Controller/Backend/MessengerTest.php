<?php

declare(strict_types=1);

namespace Concrete\Tests\Controller\Backend;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\ServerInterface;
use Concrete\Tests\TestCase;
use Core;

class MessengerTest extends TestCase
{
    public function testUnauthorizedResponse()
    {
        $url = 'http://www.dummyco.com/ccm/system/messenger/consume';

        $server = Core::make(ServerInterface::class);

        $request = Request::create($url, 'GET', []);
        $response = $server->handleRequest($request);

        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($response->getContent(), json_encode("Access Denied"));
    }
}
