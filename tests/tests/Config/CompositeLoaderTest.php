<?php

namespace Concrete\Tests\Config;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\CompositeLoader;
use Concrete\Core\Config\LoaderInterface;
use \Mockery as M;

class CompositeLoaderTest extends \PHPUnit_Framework_TestCase
{

    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $app;
    protected $composite;
    protected $loader1;
    protected $loader2;
    protected $loader3;

    public function setUp()
    {
        $this->loader1 = M::mock(LoaderInterface::class);
        $this->loader2 = M::mock(LoaderInterface::class);
        $this->loader3 = M::mock(LoaderInterface::class);

        $this->app = M::mock(Application::class);
        $this->composite = new CompositeLoader($this->app, [
            $this->loader1,
            $this->loader2,
            $this->loader3,
        ]);
    }

    public function tearDown()
    {
        $this->loader1 = null;
        $this->loader2 = null;
        $this->loader3 = null;
    }

    /**
     * @dataProvider simpleFlowMethods
     */
    public function testFlowsThrough($method, $args, $return, callable $expect = null)
    {
        $this->loader1->shouldReceive($method)->once()->withArgs($args)->andReturn($return[0]);
        $this->loader2->shouldReceive($method)->once()->withArgs($args)->andReturn($return[1]);
        $this->loader3->shouldReceive($method)->once()->withArgs($args)->andReturn($return[2]);

        $result = call_user_func_array([$this->composite, $method], $args);

        if ($expect) {
            $expect($result);
        }

        parent::assertPostConditions();
    }

    public function simpleFlowMethods()
    {
        $matches = function($match) {
            return function($results) use ($match) {
                $this->assertEquals($match, $results);
            };
        };

        return [
            ['clearNamespace', ['foo'], [true, true, true]],
            ['exists', ['foo', 'baz'], [false, false, false]],
            ['addNamespace', ['foo', 'baz'], [true, true, true]],
            ['getNamespaces', [], [['foo', 'baz'], [], ['bar']], $matches(['foo', 'baz', 'bar'])],
            ['cascadePackage', ['foo', 'bar', 'baz', 'boo'], [['foo', 'baz'], [], ['bar']], $matches(['foo', 'baz', 'bar'])],
            [
                'load',
                ['foo', 'baz', 'bar'], [['test' => true, 'overrideWorks' => false, 'subtest' => ['subtest' => 'works']],
                ['overrideWorks' => true], ['subtest' => ['stillWorks' => true]]],
                $matches([
                    'test' => true,
                    'overrideWorks' => true,
                    'subtest' => [
                        'subtest' => 'works',
                        'stillWorks' => true
                    ]
                ])
            ],
        ];
    }

    public function testInflating()
    {
        $loader = new CompositeLoader($this->app, [
            'foo',
            'baz',
            'bar',
        ]);

        $this->loader1->shouldReceive('exists')->with('foo', null)->times(3);

        $this->app->shouldReceive('make')->once()->with('foo')->andReturn($this->loader1);
        $this->app->shouldReceive('make')->once()->with('baz')->andReturn($this->loader1);
        $this->app->shouldReceive('make')->once()->with('bar')->andReturn($this->loader1);

        $loader->exists('foo');
    }

    public function testLoad()
    {

    }

}
