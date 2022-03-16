<?php

namespace Concrete\Tests\Page\Container;

use Concrete\Core\Filesystem\Icon\Icon;
use Concrete\Core\Page\Container\IconRepository;
use Concrete\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery as M;

class ContainerIconRepositoryTest extends TestCase
{
    
    public function testGetIcons()
    {
        /**
         * @var $filesystem Filesystem
         */
        $filesystem = M::mock(Filesystem::class)->makePartial();
        $repository = new IconRepository($filesystem);
        $icons = $repository->getIcons();
        $this->assertIsArray($icons);
        $this->assertCount(13, $icons);
        $icon = $icons[0];
        $this->assertInstanceOf(Icon::class, $icon);
        $this->assertEquals('blank.png', $icon->getFilename());
    }
}
