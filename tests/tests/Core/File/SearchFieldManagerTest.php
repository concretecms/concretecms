<?php

namespace Concrete\Tests\Core\File;

use Concrete\Core\Search\Field\Manager;

class SearchFieldManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGroups()
    {
        $manager = Manager::get('file');
        $this->assertInstanceOf('Concrete\Core\File\Search\Field\Manager', $manager);

        $groups = $manager->getGroups();

        $this->assertEquals(2, $groups);
        $this->assertEquals('Core Properties', $groups[0]->getGroupName());
        $this->assertEquals('Custom Attributes', $groups[1]->getGroupName());
        $this->assertInstanceOf('Concrete\Core\Search\Field\GroupInterface', $groups[0]);
        $this->assertInstanceOf('Concrete\Core\Search\Field\GroupInterface', $groups[1]);

    }

}
