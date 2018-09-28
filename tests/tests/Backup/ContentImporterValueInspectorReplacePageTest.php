<?php

namespace Concrete\Tests\Backup;

use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Backup\CustomPageRoutine;
use Concrete\TestHelpers\Page\PageTestCase;
use Core;

class ContentImporterValueInspectorReplacePageTest extends PageTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Page\Feed',
        ]);
    }

    public function testReplaceContent()
    {
        $this->createData();

        $content = <<<EOL
        <p>This is a content block. Here is a feed. <a href="{ccm:export:pagefeed:blog}">Feed</a>. It is amazing. <a href="{ccm:export:page:/page-2/subpage-b}">Link 1</a>. <a href="{ccm:export:page:}">Home</a>. Here's another. <a href="{ccm:export:page:/page-2/subpage-b}">Link 2</a>. Don't forget a second <a href="{ccm:export:page:/page-4}">link.</a>. It's a pretty good one. <a href="thumbs_up.html">Thumbs up!</a> Excellent! <a href="{ccm:export:page:/}">See you later!</a>
EOL;

        $link1 = Page::getByPath('/page-2/subpage-b')->getCollectionID();
        $link2 = Page::getByPath('/page-4')->getCollectionID();
        $link3 = Page::getHomePageID();

        $inspector = Core::make('import/value_inspector');
        $result = $inspector->inspect($content);
        $content = trim($result->getReplacedContent());
        $this->assertEquals('<p>This is a content block. Here is a feed. <a href="http://www.dummyco.com/path/to/server/index.php/rss/blog">Feed</a>. It is amazing. <a href="{CCM:CID_' . $link1 . '}">Link 1</a>. <a href="{CCM:CID_' . $link3 . '}">Home</a>. Here\'s another. <a href="{CCM:CID_' . $link1 . '}">Link 2</a>. Don\'t forget a second <a href="{CCM:CID_' . $link2 . '}">link.</a>. It\'s a pretty good one. <a href="thumbs_up.html">Thumbs up!</a> Excellent! <a href="{CCM:CID_' . $link3 . '}">See you later!</a>', $content);
    }

    public function testCustomReplaceContent()
    {
        $this->createData();

        $content = <<<EOL
        <p>This is a content block. We are testing this with a custom handler that can assume all pages in the content are under page 2. <a href="{ccm:export:page:/subpage-b}">Subpage B</a> is first. Now we <a href="{ccm:export:page:/subpage-c}">do subpage C</a></p>
EOL;

        $link1 = Page::getByPath('/page-2/subpage-b')->getCollectionID();
        $link2 = Page::getByPath('/page-2/subpage-c')->getCollectionID();

        $inspector = Core::make('import/value_inspector');
        $inspector->registerInspectionRoutine(new CustomPageRoutine());
        $result = $inspector->inspect($content);
        $content = trim($result->getReplacedContent());
        $this->assertEquals('<p>This is a content block. We are testing this with a custom handler that can assume all pages in the content are under page 2. <a href="{CCM:CID_' . $link1 . '}">Subpage B</a> is first. Now we <a href="{CCM:CID_' . $link2 . '}">do subpage C</a></p>', $content);

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
        $this->assertInstanceOf(Page::class, $o->getContentObject());
        $this->assertEquals('Page 3', $o->getContentObject()->getCollectionName());
        $this->assertEquals(Page::getByPath('/page-3')->getCollectionID(), $result->getReplacedValue());

        $result = $inspector->inspect('{ccm:export:pagefeed:blog}');
        $items = $result->getMatchedItems();
        $o = $items[0];
        $this->assertInstanceOf('\Concrete\Core\Entity\Page\Feed', $o->getContentObject());
        $this->assertEquals(1, $o->getContentObject()->getID());
        $this->assertEquals('blog', $o->getContentObject()->getHandle());
        $this->assertEquals(1, $result->getReplacedValue());
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

        $feed = new \Concrete\Core\Entity\Page\Feed();
        $feed->setHandle('blog');
        $feed->setParentID(1);
        $feed->setTitle('Title');
        $feed->setDescription('');
        \ORM::entityManager('core')->persist($feed);
        \ORM::entityManager('core')->flush();
    }
}
