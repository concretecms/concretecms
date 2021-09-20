<?php

namespace Concrete\Tests\Html\Service;

use Concrete\Core\Html\Service\FontAwesomeIcon;
use Concrete\Tests\TestCase;

class FontAwesomeIconTest extends TestCase
{
    public function testBasicUsage()
    {
        $icon = new FontAwesomeIcon('example');
        $this->assertEquals('<i class="fas fa-example"></i>', (string) $icon);

        $icon->setPrefix(FontAwesomeIcon::PREFIX_BRANDS);
        $this->assertEquals('<i class="fab fa-example"></i>', (string) $icon);

        $icon->setSize('xs');
        $this->assertEquals('<i class="fab fa-example fa-xs"></i>', (string) $icon);

        $icon = new FontAwesomeIcon('example');
        $icon->setFixedWidth(true);
        $this->assertEquals('<i class="fas fa-example fa-fw"></i>', (string) $icon);

        $icon->setFixedWidth(false);
        $icon->setRotate(90);
        $this->assertEquals('<i class="fas fa-example fa-rotate-90"></i>', (string) $icon);

        $icon->setFlip('horizontal');
        $this->assertEquals('<span class="fa-rotate-90"><i class="fas fa-example fa-flip-horizontal"></i></span>', (string) $icon);

        $icon->setRotate(0);
        $this->assertEquals('<i class="fas fa-example fa-flip-horizontal"></i>', (string) $icon);

        $icon = new FontAwesomeIcon('example');
        $icon->setSpin(true);
        $this->assertEquals('<i class="fas fa-example fa-spin"></i>', (string) $icon);

        $icon = new FontAwesomeIcon('example');
        $icon->setPulse(true);
        $this->assertEquals('<i class="fas fa-example fa-pulse"></i>', (string) $icon);

        $icon = new FontAwesomeIcon('example');
        $icon->setBordered(true);
        $icon->setPull('right');
        $this->assertEquals('<i class="fas fa-example fa-border fa-pull-right"></i>', (string) $icon);
    }

    public function testMigrateOldIconName()
    {
        $icon = new FontAwesomeIcon('500px');
        $this->assertEquals('<i class="fab fa-500px"></i>', (string) $icon);

        $icon = new FontAwesomeIcon('500px');
        $icon->setMigrateOldName(false);
        $this->assertEquals('<i class="fas fa-500px"></i>', (string) $icon);

        $icon = new FontAwesomeIcon('address-book-o');
        $this->assertEquals('<i class="fas fa-address-book"></i>', (string) $icon);

        $icon->setPro(true);
        $this->assertEquals('<i class="far fa-address-book"></i>', (string) $icon);
    }

    public function testGetFromClassNames()
    {
        $icon = FontAwesomeIcon::getFromClassNames('fa fa-example');
        $this->assertEquals('<i class="fas fa-example"></i>', (string) $icon);

        $icon = FontAwesomeIcon::getFromClassNames('fab fa-500px');
        $this->assertEquals('<i class="fab fa-500px"></i>', (string) $icon);

        $icon = FontAwesomeIcon::getFromClassNames('example');
        $this->assertEquals('<i class="fas fa-example"></i>', (string) $icon);
    }
}
