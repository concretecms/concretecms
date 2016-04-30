<?php

namespace Concrete\Tests\Core\File;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\File\Search\Field\Field\SizeField;
use Concrete\Core\File\Search\Field\Manager;
use Concrete\Core\Search\Field\ManagerFactory;

class SearchFieldManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGroups()
    {
        $manager = new \Concrete\Core\Search\Field\Manager();
        $this->assertInstanceOf('Concrete\Core\Search\Field\Manager', $manager);

        $groups = $manager->getGroups();
        $this->assertEquals(0, count($groups));

        $manager->addGroup('Test Group', [

        ]);
        $groups = $manager->getGroups();
        $this->assertEquals(1, count($groups));
        $this->assertEquals('Test Group', $groups[0]->getName());
        $this->assertEquals(0, count($groups[0]->getFields()));
    }

    public function testFields()
    {
        $manager = new \Concrete\Core\Search\Field\Manager();
        $manager->addGroup('Test Group', [
            new SizeField()
        ]);
        $groups = $manager->getGroups();
        $this->assertEquals(1, count($groups[0]->getFields()));

        $groups[0]->addField(new SizeField());
        $this->assertEquals(2, count($groups[0]->getFields()));
    }


    public function testFileSearchFields()
    {
        $category = $this
            ->getMockBuilder('\Concrete\Core\Attribute\Category\FileCategory')
            ->disableOriginalConstructor()
            ->getMock();

        $type = new \Concrete\Core\Entity\Attribute\Type();
        $type->setAttributeTypeHandle('text');
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\TextType();
        $first_name = new \Concrete\Core\Entity\Attribute\Key\FileKey();
        $first_name->setAttributeKeyHandle('first_name');
        $first_name->setAttributeKeyName(t('First Name'));
        $first_name->setAttributeKeyType($key_type);

        $type = new \Concrete\Core\Entity\Attribute\Type();
        $type->setAttributeTypeHandle('boolean');
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\BooleanType();
        $boolean = new \Concrete\Core\Entity\Attribute\Key\FileKey();
        $boolean->setAttributeKeyHandle('is_awesome');
        $boolean->setAttributeKeyName(t('Is Awesome'));
        $boolean->setAttributeKeyType($key_type);

        $attributes = [$first_name, $boolean];

        $category->expects($this->any())
            ->method('getSearchableList')
            ->will($this->returnValue($attributes));

        $manager = new Manager($category);
        $groups = $manager->getGroups();

        $this->assertEquals(2, count($groups));
        $this->assertEquals('Core Properties', $groups[0]->getName());
        $this->assertEquals('Custom Attributes', $groups[1]->getName());
        $this->assertInstanceOf('Concrete\Core\Search\Field\GroupInterface', $groups[0]);
        $this->assertInstanceOf('Concrete\Core\Search\Field\GroupInterface', $groups[1]);
        $this->assertEquals(5, count($groups[0]->getFields()));
        $this->assertEquals(2, count($groups[1]->getFields()));

        $field1 = $groups[0]->getFields()[3];
        $this->assertEquals('Date Added', $field1->getDisplayName());
        $this->assertEquals('date_added', $field1->getKey());

        $field2 = $groups[1]->getFields()[0];
        $field3 = $groups[1]->getFields()[1];

        $this->assertEquals('First Name', $field2->getDisplayName());
        $this->assertEquals('attribute_key_first_name', $field2->getKey());

        $this->assertEquals('Is Awesome', $field3->getDisplayName());
        $this->assertEquals('attribute_key_is_awesome', $field3->getKey());
    }

}
