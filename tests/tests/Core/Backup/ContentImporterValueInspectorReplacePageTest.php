<?php

class CustomPageRoutine extends \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine
{

    public function getItem($identifier)
    {
        $identifier = '/page-2' . $identifier;
        return new \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem($identifier);
    }

}

class ContentImporterValueInspectorReplacePageTest extends PageTestCase
{

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'PageFeeds'
        ));
        parent::setUp();
    }

    protected function createData()
    {
        $page1 = self::createPage('Page 1');
        $page2 = self::createPage('Page 2');
        $page3 = self::createPage('Page 3');
        $page4 = self::createPage('Page 4');

        $subpageA = self::createPage('Subpage A', $page2);
        self::createPage('Subpage B', $page2);
        self::createPage('Subpage C', $page2);

        $feed = new \Concrete\Core\Page\Feed();
        $feed->setHandle('blog');
        $feed->setParentID(1);
        \ORM::entityManager('core')->persist($feed);
        \ORM::entityManager('core')->flush();

    }

    public function testReplaceContent()
    {

        $this->createData();

        $content = <<<EOL
        <p>This is a content block. Here is a feed. <a href="{ccm:export:pagefeed:blog}">Feed</a>. It is amazing. <a href="{ccm:export:page:/page-2/subpage-b}">Link 1</a>. <a href="{ccm:export:page:}">Home</a>. Here's another. <a href="{ccm:export:page:/page-2/subpage-b}">Link 2</a>. Don't forget a second <a href="{ccm:export:page:/page-4}">link.</a>. It's a pretty good one. <a href="thumbs_up.html">Thumbs up!</a> Excellent! <a href="{ccm:export:page:/}">See you later!</a>
EOL;

        $inspector = Core::make('import/value_inspector');
        $result = $inspector->inspect($content);
        $content = trim($result->getReplacedContent());
        $this->assertEquals('<p>This is a content block. Here is a feed. <a href="http://www.dummyco.com/path/to/server/index.php/rss/blog">Feed</a>. It is amazing. <a href="{CCM:CID_7}">Link 1</a>. <a href="{CCM:CID_1}">Home</a>. Here\'s another. <a href="{CCM:CID_7}">Link 2</a>. Don\'t forget a second <a href="{CCM:CID_5}">link.</a>. It\'s a pretty good one. <a href="thumbs_up.html">Thumbs up!</a> Excellent! <a href="{CCM:CID_1}">See you later!</a>', $content);
    }

    public function testCustomReplaceContent()
    {

        $this->createData();

        $content = <<<EOL
        <p>This is a content block. We are testing this with a custom handler that can assume all pages in the content are under page 2. <a href="{ccm:export:page:/subpage-b}">Subpage B</a> is first. Now we <a href="{ccm:export:page:/subpage-c}">do subpage C</a></p>
EOL;

        $inspector = Core::make('import/value_inspector');
        $inspector->registerInspectionRoutine(new CustomPageRoutine());
        $result = $inspector->inspect($content);
        $content = trim($result->getReplacedContent());
        $this->assertEquals('<p>This is a content block. We are testing this with a custom handler that can assume all pages in the content are under page 2. <a href="{CCM:CID_7}">Subpage B</a> is first. Now we <a href="{CCM:CID_8}">do subpage C</a></p>', $content);

        $inspector->registerInspectionRoutine(new \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine());
    }

    public function testMatchedObjects()
    {
        $this->createData();
        $inspector = Core::make('import/value_inspector');
        $content = '{ccm:export:page:/page-3}';
        $result = $inspector->inspect($content);
        $items = $result->getMatchedItems();
        $o = $items[0];
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $o->getContentObject());
        $this->assertEquals('Page 3', $o->getContentObject()->getCollectionName());
        $this->assertEquals(4, $result->getReplacedValue());

        $result = $inspector->inspect('{ccm:export:pagefeed:blog}');
        $items = $result->getMatchedItems();
        $o = $items[0];
        $this->assertInstanceOf('\Concrete\Core\Page\Feed', $o->getContentObject());
        $this->assertEquals(1, $o->getContentObject()->getID());
        $this->assertEquals('blog', $o->getContentObject()->getHandle());
        $this->assertEquals(1, $result->getReplacedValue());

    }

}
