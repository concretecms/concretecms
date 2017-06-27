<?php

namespace Concrete\Tests\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Runtime\Run\DefaultRunner;
use Concrete\Core\Http\ServerInterface;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Config\Repository\Liaison;
use Illuminate\Filesystem\Filesystem;
use Concrete\Tests\Core\Config\Fixtures\TestFileLoader;
use Concrete\Tests\Core\Config\Fixtures\TestFileSaver;
use Concrete\Core\Config\Repository\Repository;

class DefaultRunnerTest extends PHPUnit_Framework_TestCase
{

    public function testReturnsResponseWhenNotInstalled()
    {
        // Setup Response
        $expectedResponse = $this->getMock(Response::class);
        $expectedResponse->expects($this->once())->method('prepare')->willReturnSelf();

        // Create a mock server
        $server = $this->getMock(ServerInterface::class);
        $server->method('handleRequest')->willReturn($expectedResponse);

        // Create a mock application
        $fs = new Filesystem();
        $config = new Liaison(
            new Repository(new TestFileLoader($fs), new TestFileSaver($fs), 'test'),
            'default'
        );
        
        $app = $this->getMock(Application::class);
        $app->method('isInstalled')->willReturn(false);
        $app->method('make')->will(
            $this->returnValueMap([
                ['config', [], $config],
            ])
        );

        // Create the runner to test
        $runner = new DefaultRunner($server);
        $runner->setApplication($app);

        // Test running while not installed
        $this->assertEquals($expectedResponse, $runner->run());
    }
}
