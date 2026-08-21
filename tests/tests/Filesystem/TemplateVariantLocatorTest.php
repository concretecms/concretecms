<?php

namespace Concrete\Tests\Filesystem;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\FileLocator\LocationInterface;
use Concrete\Core\Filesystem\FileLocator\Record;
use Concrete\Core\Filesystem\TemplateVariantLocator;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\Facade;
use Illuminate\Filesystem\Filesystem;
use Concrete\Tests\TestCase;

class TemplateVariantLocatorTest extends TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function setUp(): void
    {
        $this->app = Facade::getFacadeApplication();
    }

    public function testExtensionlessPathsAreTreatedAsLegacyPhpRequests()
    {
        $fileLocator = $this->createMock(FileLocator::class);
        $fileLocator->expects($this->once())
            ->method('getFilesystem')
            ->willReturn(new Filesystem());
        $location = new TemplateVariantTestLocation('custom', [
            DIRNAME_ELEMENTS . '/foo.php' => true,
        ]);
        $fileLocator->expects($this->once())
            ->method('getSearchLocations')
            ->willReturn([$location]);
        $locator = new TemplateVariantLocator($fileLocator);

        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/foo');

        $this->assertEquals('/custom/' . DIRNAME_ELEMENTS . '/foo.php', $record->getFile());
        $this->assertSame([DIRNAME_ELEMENTS . '/foo.html.twig', DIRNAME_ELEMENTS . '/foo.php'], $location->getLookups());
    }

    public function testExplicitTwigRequestOnlyChecksTwig()
    {
        $fileLocator = $this->createMock(FileLocator::class);
        $fileLocator->expects($this->once())
            ->method('getFilesystem')
            ->willReturn(new Filesystem());
        $location = new TemplateVariantTestLocation('custom', [
            DIRNAME_ELEMENTS . '/foo.html.twig' => true,
            DIRNAME_ELEMENTS . '/foo.php' => true,
        ]);
        $fileLocator->expects($this->once())
            ->method('getSearchLocations')
            ->willReturn([$location]);

        $locator = new TemplateVariantLocator($fileLocator);
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/foo.html.twig');

        $this->assertEquals('/custom/' . DIRNAME_ELEMENTS . '/foo.html.twig', $record->getFile());
        $this->assertSame([DIRNAME_ELEMENTS . '/foo.html.twig'], $location->getLookups());
    }

    public function testPhpRequestPrefersExistingTwigVariantWithinSameLocation()
    {
        $fileLocator = $this->createMock(FileLocator::class);
        $fileLocator->expects($this->once())
            ->method('getFilesystem')
            ->willReturn(new Filesystem());
        $location = new TemplateVariantTestLocation('custom', [
            DIRNAME_ELEMENTS . '/foo.html.twig' => true,
            DIRNAME_ELEMENTS . '/foo.php' => true,
        ]);
        $fileLocator->expects($this->once())
            ->method('getSearchLocations')
            ->willReturn([$location]);

        $locator = new TemplateVariantLocator($fileLocator);
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/foo.php');

        $this->assertEquals('/custom/' . DIRNAME_ELEMENTS . '/foo.html.twig', $record->getFile());
        $this->assertSame([DIRNAME_ELEMENTS . '/foo.html.twig'], $location->getLookups());
    }

    public function testPhpRequestFallsBackToExistingPhpVariantWithinSameLocation()
    {
        $fileLocator = $this->createMock(FileLocator::class);
        $fileLocator->expects($this->once())
            ->method('getFilesystem')
            ->willReturn(new Filesystem());
        $location = new TemplateVariantTestLocation('custom', [
            DIRNAME_ELEMENTS . '/foo.php' => true,
        ]);
        $fileLocator->expects($this->once())
            ->method('getSearchLocations')
            ->willReturn([$location]);

        $locator = new TemplateVariantLocator($fileLocator);
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/foo.php');

        $this->assertEquals('/custom/' . DIRNAME_ELEMENTS . '/foo.php', $record->getFile());
        $this->assertSame([DIRNAME_ELEMENTS . '/foo.html.twig', DIRNAME_ELEMENTS . '/foo.php'], $location->getLookups());
    }

    public function testPhpRequestFallsBackToRequestedPhpRecordWhenNothingExistsInWinningLocation()
    {
        $fileLocator = $this->createMock(FileLocator::class);
        $fileLocator->expects($this->once())
            ->method('getFilesystem')
            ->willReturn(new Filesystem());
        $location = new TemplateVariantTestLocation('package-like', [
            DIRNAME_ELEMENTS . '/foo.html.twig' => false,
            DIRNAME_ELEMENTS . '/foo.php' => false,
        ]);
        $fileLocator->expects($this->once())
            ->method('getSearchLocations')
            ->willReturn([$location]);

        $locator = new TemplateVariantLocator($fileLocator);
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/foo.php');

        $this->assertEquals('/package-like/' . DIRNAME_ELEMENTS . '/foo.php', $record->getFile());
        $this->assertSame([DIRNAME_ELEMENTS . '/foo.html.twig', DIRNAME_ELEMENTS . '/foo.php'], $location->getLookups());
    }

    public function testHigherPriorityFallbackBeatsLowerPriorityExistingLocation()
    {
        $fileLocator = $this->createMock(FileLocator::class);
        $fileLocator->expects($this->once())
            ->method('getFilesystem')
            ->willReturn(new Filesystem());
        $highPriority = new TemplateVariantTestLocation('package-like', [
            DIRNAME_ELEMENTS . '/foo.php' => false,
        ]);
        $lowPriority = new TemplateVariantTestLocation('core-like', [
            DIRNAME_ELEMENTS . '/foo.php' => true,
        ]);
        $fileLocator->expects($this->once())
            ->method('getSearchLocations')
            ->willReturn([$highPriority, $lowPriority]);

        $locator = new TemplateVariantLocator($fileLocator);
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/foo.php');

        $this->assertEquals('/package-like/' . DIRNAME_ELEMENTS . '/foo.php', $record->getFile());
        $this->assertSame([DIRNAME_ELEMENTS . '/foo.html.twig', DIRNAME_ELEMENTS . '/foo.php'], $highPriority->getLookups());
        $this->assertSame([], $lowPriority->getLookups());
    }

    public function testApplicationExistingTemplateBeatsPackageFallback()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->once())
            ->method('exists')
            ->willReturnMap([
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/foo.html.twig', true],
            ]);

        $fileLocator = $this->app->make(FileLocator::class);
        $fileLocator->setFilesystem($filesystem);
        $fileLocator->addPackageLocation('awesome');

        $record = (new TemplateVariantLocator($fileLocator))->getRecord(DIRNAME_ELEMENTS . '/foo.php');

        $this->assertEquals(DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/foo.html.twig', $record->getFile());
        $this->assertTrue($record->exists());
    }

    public function testThemeExistingTemplateBeatsPackageFallback()
    {
        $theme = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->getMock();
        $theme->expects($this->any())
            ->method('getThemeHandle')
            ->willReturn('brilliant');
        $theme->expects($this->once())
            ->method('getPackageHandle')
            ->willReturn('brilliant_theme');

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->exactly(3))
            ->method('exists')
            ->willReturnMap([
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/foo.html.twig', false],
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/foo.php', false],
                [DIR_PACKAGES . '/brilliant_theme/themes/brilliant/' . DIRNAME_ELEMENTS . '/foo.html.twig', true],
            ]);

        $fileLocator = $this->app->make(FileLocator::class);
        $fileLocator->setFilesystem($filesystem);
        $fileLocator->addLocation(new FileLocator\ThemeElementLocation($theme));
        $fileLocator->addPackageLocation('awesome');

        $record = (new TemplateVariantLocator($fileLocator))->getRecord(DIRNAME_ELEMENTS . '/foo.php');

        $this->assertEquals(DIR_PACKAGES . '/brilliant_theme/themes/brilliant/' . DIRNAME_ELEMENTS . '/foo.html.twig', $record->getFile());
        $this->assertTrue($record->exists());
    }

    public function testPackageFallbackBeatsCoreCandidateForLegacyTraversalPath()
    {
        $request = DIRNAME_ELEMENTS . '/../blocks/html/templates/safe_2/view.php';
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->exactly(4))
            ->method('exists')
            ->willReturnMap([
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/../blocks/html/templates/safe_2/view.html.twig', false],
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/../blocks/html/templates/safe_2/view.php', false],
                [DIR_PACKAGES . '/awesome/' . DIRNAME_ELEMENTS . '/../blocks/html/templates/safe_2/view.html.twig', false],
                [DIR_PACKAGES . '/awesome/' . DIRNAME_ELEMENTS . '/../blocks/html/templates/safe_2/view.php', false],
            ]);

        $fileLocator = $this->app->make(FileLocator::class);
        $fileLocator->setFilesystem($filesystem);
        $fileLocator->addPackageLocation('awesome');

        $record = (new TemplateVariantLocator($fileLocator))->getRecord($request);

        $this->assertEquals(DIR_PACKAGES . '/awesome/' . DIRNAME_ELEMENTS . '/../blocks/html/templates/safe_2/view.php', $record->getFile());
        $this->assertFalse($record->exists());
    }
}

final class TemplateVariantTestLocation implements LocationInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array<string, bool>
     */
    private $responses;

    /**
     * @var Filesystem|null
     */
    private $filesystem;

    /**
     * @var string[]
     */
    private $lookups = [];

    /**
     * @param array<string, bool> $responses
     */
    public function __construct(string $name, array $responses)
    {
        $this->name = $name;
        $this->responses = $responses;
    }

    public function getCacheKey()
    {
        return $this->name;
    }

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function contains($file)
    {
        $this->lookups[] = $file;
        if (!array_key_exists($file, $this->responses)) {
            return false;
        }

        $record = new Record($this->filesystem ?? new Filesystem());
        $record->setFile('/' . $this->name . '/' . $file);
        $record->setUrl('/' . $this->name . '/' . $file);
        $record->setExists($this->responses[$file]);

        return $record;
    }

    /**
     * @return string[]
     */
    public function getLookups(): array
    {
        return $this->lookups;
    }
}
