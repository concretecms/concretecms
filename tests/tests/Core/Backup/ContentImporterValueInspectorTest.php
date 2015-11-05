<?php

class ContentImporterValueInspectorTest extends PHPUnit_Framework_TestCase
{

    public function providerMatchedSimpleValues()
    {
        return array(
            array('{ccm:export:page:/ok/here/we-go}', '/ok/here/we-go', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem'),
            array('{ccm:export:file:house.jpg}', 'house.jpg', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem'),
            array('{ccm:export:pagetype:blog}', 'blog', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem'),
            array('{ccm:export:pagefeed:rss}', 'rss', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem'),
            array('{ccm:export:image:my_cool_pic.jpg}', 'my_cool_pic.jpg', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem'),
            array('<concrete-picture file="avatar.jpg"></concrete-picture>', 'avatar.jpg', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem'),
        );
    }

    /**
     * @dataProvider providerMatchedSimpleValues
     */
    public function testMatchedSimpleValues($content, $reference, $itemClass)
    {
        $inspector = new \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector($content);
        $item = $inspector->getMatchedItem();
        $this->assertEquals($reference, $item->getReference());
        $this->assertInstanceOf($itemClass, $item);
    }

    public function testMatchedContentPageAndImage()
    {
        $content = <<<EOL
        <p>This is a content block. It is amazing. <a href="{ccm:export:page:/path/to/page}">Link 1</a>.
        Don't forget a second <a href="{ccm:export:page:/about}">link.</a>. Also, we're going to embed a picture
        here too. <concrete-picture alt="cats are cool"  file="cats.jpg">. It's a pretty good one. <a href="thumbs_up.html">Thumbs up!</a>

        Excellent! <a href="{ccm:export:page:/}">See you later!</a>
EOL;

        $inspector = new \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector($content);
        $items = $inspector->getMatchedItems();
        $this->assertEquals(4, count($items));
        $this->assertEquals($items[0]->getReference(), 'cats.jpg');
        $this->assertEquals($items[1]->getReference(), '/path/to/page');
        $this->assertEquals($items[2]->getReference(), '/about');
        $this->assertEquals($items[3]->getReference(), '/');
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem', $items[0]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem', $items[1]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem', $items[2]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem', $items[3]);
    }

    public function testMatchedContentFilePageTypePageFeed()
    {
        $content = <<<EOL
        <p>Here is a link to an <a href="{ccm:export:pagefeed:blog}">rss feed</a>. We're also linking to a
        <a href="{ccm:export:file:filename1.jpg}">couple</a> of <A href="{ccm:export:file:filename2.JPG}">files.</a>.
        Finally, we're also going to link to a pagetype here: {ccm:export:pagetype:blog_entry}.
EOL;

        $inspector = new \Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector($content);
        $items = $inspector->getMatchedItems();
        $this->assertEquals(4, count($items));
        $this->assertEquals($items[0]->getReference(), 'filename1.jpg');
        $this->assertEquals($items[1]->getReference(), 'filename2.JPG');
        $this->assertEquals($items[2]->getReference(), 'blog_entry');
        $this->assertEquals($items[3]->getReference(), 'blog');
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem', $items[0]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem', $items[1]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem', $items[2]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem', $items[3]);
    }
}
