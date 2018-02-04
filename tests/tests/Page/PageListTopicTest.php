<?php

namespace Concrete\Tests\Page;

use Concrete\TestHelpers\Page\PageTestCase;
use Group;
use Page;

class PageListTopicTest extends PageTestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'TreeTypes',
            'TopicTrees',
            'TreeNodes',
            'TreeNodeTypes',
            'Trees',
            'TreeGroupNodes',
            'PermissionAccessEntityTypes',
            'PermissionAccessEntities',
            'PermissionAccessEntityGroups',
            'Groups',
            'TreeNodePermissionAssignments',
        ]);
        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\TopicsValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectedTopic',
            'Concrete\Core\Entity\Attribute\Key\Settings\TopicsSettings',
            'Concrete\Core\Entity\Page\Feed',
        ]);
    }

    public function setUp()
    {
        parent::setUp();

        $g1 = Group::add(
            tc('GroupName', 'Guest'),
            tc('GroupDescription', 'The guest group represents unregistered visitors to your site.'),
            false,
            false,
            GUEST_GROUP_ID);


        \Concrete\Core\Attribute\Type::add('topics', 'Topic');
        \Concrete\Core\Tree\TreeType::add('topic');
        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('topic');

        \Concrete\Core\Permission\Access\Entity\Type::add('group', 'Group');

        $tree = \Concrete\Core\Tree\Type\Topic::add('Calendar Categories');
        $node = $tree->getRootTreeNodeObject();

        \Concrete\Core\Tree\Node\Type\Topic::add('Test Topic', $node);
        $node = $tree->getRootTreeNodeObject();

        $attributeType = \Concrete\Core\Attribute\Type::getByHandle('topics');

        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\TopicsSettings();
        $settings->setTopicTreeID($node->getTreeID());
        $settings->setTopicTreeID($node->getTreeID());
        $settings->setParentNodeID($node->getTreeNodeID());

        $category = \Concrete\Core\Attribute\Key\Category::add('collection');

        $key = $category->createAttributeKey();
        $key->setAttributeKeyHandle('topics');
        $key->setAttributeKeyName('Topics');
        $key->setAttributeType($attributeType);

        $settings->setAttributeKey($key);

        $topics = $category->add($attributeType, $key, $settings);
    }

    public function testFilterByTopic()
    {
        $topic = \Concrete\Core\Tree\Node\Type\Topic::add('Summer');
        $home = Page::getByID(Page::getHomePageID());

        $home->setAttribute('topics', $topic);

        $list = new \Concrete\Core\Page\PageList();
        $list->ignorePermissions();
        $total = $list->getTotalResults();

        $this->assertEquals(1, $total);

        $list->filterByTopic($topic);
        $this->assertEquals(1, $list->getTotalResults());
    }
}
