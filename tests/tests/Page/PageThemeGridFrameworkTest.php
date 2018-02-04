<?php

namespace Concrete\Tests\Page;

use Core;
use PHPUnit_Framework_TestCase;

class PageThemeGridFrameworkTest extends PHPUnit_Framework_TestCase
{
    public function testGridFrameworkManagerLoading()
    {
        $gf = Core::make('manager/grid_framework');
        $this->assertInstanceOf('\Concrete\Core\Page\Theme\GridFramework\Manager', $gf);
        $this->assertInstanceOf('\Concrete\Core\Page\Theme\GridFramework\Type\NineSixty', $gf->driver('nine_sixty'));
        $this->assertInstanceOf('\Concrete\Core\Page\Theme\GridFramework\Type\Bootstrap2', $gf->driver('bootstrap2'));
        $this->assertInstanceOf('\Concrete\Core\Page\Theme\GridFramework\Type\Bootstrap3', $gf->driver('bootstrap3'));
        $this->assertInstanceOf('\Concrete\Core\Page\Theme\GridFramework\Type\Foundation', $gf->driver('foundation'));
    }
}
