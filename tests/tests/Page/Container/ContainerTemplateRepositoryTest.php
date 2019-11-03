<?php

namespace Concrete\Tests\Page\Container;

use Concrete\Core\Application\Application;
use Concrete\Core\Page\Container\TemplateRepository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use Illuminate\Filesystem\Filesystem;

class ContainerTemplateRepositoryTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;

    /**
     * @expectedException Concrete\Core\Error\UserMessageException
     * @expectedExceptionMessage Container template filename cannot be empty.
     */
    public function testInvalidEmptyTemplate()
    {
        $filesystem = M::mock(Filesystem::class)->makePartial();
        $app = M::mock(Application::class);
        $theme = M::mock(Theme::class);
        $repository = new TemplateRepository($filesystem, $app);
        $this->assertFalse($repository->isValid($theme, ''));
    }

    /**
     * @expectedException Concrete\Core\Error\UserMessageException
     * @expectedExceptionMessage Container template filename must have a .php extension.
     */
    public function testInvalidExtension()
    {
        $filesystem = M::mock(Filesystem::class)->makePartial();
        $app = M::mock(Application::class);
        $theme = M::mock(Theme::class);
        $repository = new TemplateRepository($filesystem, $app);
        $this->assertFalse($repository->isValid($theme, 'bogusfile'));
    }

    /**
     * @expectedException Concrete\Core\Error\UserMessageException
     * @expectedExceptionMessage Container template file does not exist.
     */
    public function testInvalidFile()
    {
        $filesystem = M::mock(Filesystem::class)->makePartial();
        $app = M::mock(Application::class)->makePartial();
        $theme = M::mock(Theme::class);
        $theme->shouldReceive('getThemeHandle')->andReturn('elemental');
        $theme->shouldReceive('getPackageHandle')->andReturn(null);
        $repository = new TemplateRepository($filesystem, $app);
        $this->assertFalse($repository->isValid($theme, 'bogusfile.php'));
    }

    public function testValidFile()
    {
        $filesystem = M::mock(Filesystem::class)->makePartial();
        $app = M::mock(Application::class)->makePartial();
        $theme = M::mock(Theme::class);
        $theme->shouldReceive('getThemeHandle')->andReturn('elemental');
        $theme->shouldReceive('getPackageHandle')->andReturn(null);
        $repository = new TemplateRepository($filesystem, $app);
        $this->assertTrue($repository->isValid($theme, 'two_column_accent.php'));
    }

}
