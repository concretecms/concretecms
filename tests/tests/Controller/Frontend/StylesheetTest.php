<?php

declare(strict_types=1);

namespace Concrete\Tests\Controller\Frontend;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\ServerInterface;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;

class StylesheetTest extends ConcreteDatabaseTestCase
{
    protected $tables = ['AreaLayouts'];

    public function testUnexistingLayout()
    {
        $url = sprintf('http://www.dummyco.com/ccm/system/css/layout/%d', 1337);

        $server = Core::make(ServerInterface::class);

        $request = Request::create($url, 'GET', []);
        $response = $server->handleRequest($request);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getContent(), "");
    }
}
