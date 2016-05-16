<?php

class PageListTopicTest extends PageTestCase
{

    public function setUp()
    {
        $this->tables = array_merge($this->tables, array(
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
        ));
        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
            'Concrete\Core\Entity\Attribute\Key\Type\Type',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\TopicsValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectedTopic',
            'Concrete\Core\Entity\Attribute\Key\Type\TopicsType',
            'Concrete\Core\Entity\Page\Feed',
        ));


        parent::setUp();


        $g1 = Group::add(
            tc("GroupName", "Guest"),
            tc("GroupDescription", "The guest group represents unregistered visitors to your site."),
            false,
            false,
            GUEST_GROUP_ID);


        \Concrete\Core\Attribute\Type::add('topics', 'Topic');
        \Concrete\Core\Tree\TreeType::add('topic');
        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('topic');

        $tree = \Concrete\Core\Tree\Type\Topic::add('Calendar Categories');
        $node = $tree->getRootTreeNodeObject();

        \Concrete\Core\Tree\Node\Type\Topic::add('Test Topic', $node);
        $node = $tree->getRootTreeNodeObject();

        $type = new \Concrete\Core\Entity\Attribute\Key\Type\TopicsType();
        $type->setTopicTreeID($node->getTreeID());
        $type->setTopicTreeID($node->getTreeID());
        $type->setParentNodeID($node->getTreeNodeID());

        $category = \Concrete\Core\Attribute\Key\Category::add('collection');

        $key = $category->createAttributeKey();
        $key->setAttributeKeyHandle('topics');
        $key->setAttributeKeyName('Topics');
        $topics = $category->add($type, $key);
    }
    public function testFilterByTopic()
    {

        $topic = \Concrete\Core\Tree\Node\Type\Topic::add("Summer");
        $home = Page::getByID(HOME_CID);

        $home->setAttribute('topics', $topic);

        $list = new \Concrete\Core\Page\PageList();
        $list->ignorePermissions();
        $total = $list->getTotalResults();

        $this->assertEquals(1, $total);

        $list->filterByTopic($topic);
        $this->assertEquals(1, $list->getTotalResults());

    }


}
