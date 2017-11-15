<?php

namespace Concrete\Tests\Page;

use Concrete\TestHelpers\Page\SomethingCoolPageTypeSaver;
use Core;
use PHPUnit_Framework_TestCase;

class PageTypeSaverTest extends PHPUnit_Framework_TestCase
{
    public function testPageTypeSaverManagerLoading()
    {
        $manager = Core::make('manager/page_type/saver');
        $manager->extend('something_cool', function ($app) {
            return new SomethingCoolPageTypeSaver();
        });

        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\Manager', $manager);
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\StandardSaver', $manager->driver('blog_entry'));
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\StandardSaver', $manager->driver('page'));
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\SaverInterface', $manager->driver('page'));

        $this->assertInstanceOf(SomethingCoolPageTypeSaver::class, $manager->driver('something_cool'));
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\SaverInterface', $manager->driver('something_cool'));

        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\StandardSaver', $manager->driver('another'));
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\StandardSaver', $manager->driver());

        $type1 = new \Concrete\Core\Page\Type\Type();
        $type1->ptHandle = 'something_cool';
        $type2 = new \Concrete\Core\Page\Type\Type();
        $type2->ptHandle = 'blog_entry';

        $this->assertInstanceOf(SomethingCoolPageTypeSaver::class, $type1->getPageTypeSaverObject());
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Saver\StandardSaver', $type2->getPageTypeSaverObject());
        $this->assertInstanceOf('\Concrete\Core\Page\Type\Type', $type2->getPageTypeSaverObject()->getPageTypeObject());
    }
}
