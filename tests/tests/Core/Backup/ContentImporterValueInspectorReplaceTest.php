<?php

class ContentImporterValueInspectorReplaceTest extends PageTestCase
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
        \Database::connection()->getEntityManager()->persist($feed);
        \Database::connection()->getEntityManager()->flush();

    }

    public function testReplaceContent()
    {

        $this->createData();

        $content = <<<EOL
        <p>This is a content block. Here is a feed. <a href="{ccm:export:pagefeed:blog}">Feed</a>. It is amazing. <a href="{ccm:export:page:/page-2/subpage-b}">Link 1</a>. Don't forget a second <a href="{ccm:export:page:/page-4}">link.</a>. It's a pretty good one. <a href="thumbs_up.html">Thumbs up!</a> Excellent! <a href="{ccm:export:page:/}">See you later!</a>
EOL;

        $inspector = new \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector($content);
        $content = trim($inspector->getReplacedContent());

        $this->assertEquals('<p>This is a content block. Here is a feed. <a href="http://www.dummyco.com/path/to/server/index.php/rss/blog">Feed</a>. It is amazing. <a href="CCM:CID_7">Link 1</a>. Don\'t forget a second <a href="CCM:CID_5">link.</a>. It\'s a pretty good one. <a href="thumbs_up.html">Thumbs up!</a> Excellent! <a href="CCM:CID_1">See you later!</a>', $content);
    }

    public function testMatchedObjects()
    {
        $this->createData();
        $inspector = new \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector('{ccm:export:page:/page-3}');
        $o = $inspector->getMatchedItem();
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $o->getContentObject());
        $this->assertEquals('Page 3', $o->getContentObject()->getCollectionName());
        $inspector = new \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector('{ccm:export:pagefeed:blog}');
        $o = $inspector->getMatchedItem();
        $this->assertInstanceOf('\Concrete\Core\Page\Feed', $o->getContentObject());
        $this->assertEquals(1, $o->getContentObject()->getID());
        $this->assertEquals('blog', $o->getContentObject()->getHandle());

    }

}
