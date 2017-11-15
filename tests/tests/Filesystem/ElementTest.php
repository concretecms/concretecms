<?php

namespace Concrete\Tests\Filesystem;

use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use PHPUnit_Framework_TestCase;

class ElementTest extends PHPUnit_Framework_TestCase
{
    public function testBasicElementAndController()
    {
        $element = new Element('header_required');
        $this->assertTrue($element->exists());
        $this->assertNull($element->getElementController());

        $element = \Element::get('header_required');
        $this->assertTrue($element->exists());
        $this->assertNull($element->getElementController());
        $this->assertEquals(DIRNAME_ELEMENTS . '/header_required.php', $element->getElementPath());

        $element = \Element::get('workflow/type_form_required');
        $this->assertTrue($element->exists());
        $this->assertNull($element->getElementController());

        $element = \Element::get('dashboard/pages/types/header', [new Type()]);
        $this->assertInstanceOf('Concrete\Controller\Element\Dashboard\Pages\Types\Header', $element->getElementController());
        $this->assertTrue($element->exists());

        $element = \Element::get('dashboard/foo');
        $this->assertFalse($element->exists());
    }

    public function testRender()
    {
        $element = new Element('progress_bar');
        $element->set('totalItems', 20);
        $element->set('totalItemsSummary', 50);
        ob_start();
        $element->render();
        $contents = ob_get_contents();
        ob_end_clean();

        $contents = trim(preg_replace('~>\s+<~', '><', $contents));
        $this->assertEquals('<div class="ccm-ui"><div id="ccm-progressive-operation-progress-bar" data-total-items="20"><div class="progress progress-striped active"><div class="progress-bar" style="width: 0%;"></div></div></div><div><span id="ccm-progressive-operation-status">1</span> of 50</div></div>', $contents);
    }

    public function testPackageLocator()
    {
        $element = new Element('dashboard_menu', 'calendar');
        $locator = $element->getLocator();
        $this->assertInstanceOf(FileLocator::class, $locator);
        $locations = $locator->getLocations();
        $this->assertCount(1, $locations);
        $this->assertInstanceOf('\Concrete\Core\Filesystem\FileLocator\PackageLocation', $locations[0]);
        $this->assertEquals('calendar', $locations[0]->getPackageHandle());
    }

    public function testThemeLocatorAndPackageLocator()
    {
        $c = new Page();
        $theme = new Theme();
        $theme->setThemeHandle('beautiful');
        $c->themeObject = $theme;
        $element = \Element::get('mobile/menu', 'mighty_theme', $c);
        $locator = $element->getLocator();
        $locations = $locator->getLocations();
        $this->assertCount(2, $locations);
        $this->assertInstanceOf('\Concrete\Core\Filesystem\FileLocator\ThemeLocation', $locations[0]);
        $this->assertInstanceOf('\Concrete\Core\Filesystem\FileLocator\PackageLocation', $locations[1]);
        $this->assertEquals('mighty_theme', $locations[1]->getPackageHandle());
        $this->assertEquals('beautiful', $locations[0]->getThemeHandle());
    }

    public function testOverriding()
    {
        \Element::register('header_required', function () {
            $element = new Element('header_required', 'my_site');

            return $element;
        });

        $header = \Element::get('header_required');
        $locator = $header->getLocator();
        $locations = $locator->getLocations();
        $this->assertCount(1, $locations);
        $this->assertInstanceOf('\Concrete\Core\Filesystem\FileLocator\PackageLocation', $locations[0]);
        $this->assertEquals('my_site', $locations[0]->getPackageHandle());
    }
}
