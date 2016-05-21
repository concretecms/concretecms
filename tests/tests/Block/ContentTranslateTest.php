<?php

use \Concrete\Core\Editor\LinkAbstractor;

class ContentTranslateTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array(
        'SystemContentEditorSnippets',
    );

    public function setUp()
    {
        \Core::forgetInstance('url/canonical');

        return parent::setUp();
    }

    /**
     * This is saving data from the content editor HTML INTO the database.
     *
     *  @dataProvider contentsTo
     */
    public function testTo($from, $to)
    {
        $translated = LinkAbstractor::translateTo($from);
        $this->assertEquals($to, $translated);
    }

    /**
     * This is taking data OUT of the database and sending it into the content editor.
     *
     * @dataProvider contentsFromEditMode
     */
    public function testFromEditMode($to, $from)
    {
        $translated = LinkAbstractor::translateFromEditMode($from);
        $this->assertEquals($to, $translated);
    }

    /**
     * This is taking data OUT of the database and sending it into the page.
     *
     *  @dataProvider contentsFrom
     */
    public function testFrom($from, $to)
    {
        $translated = LinkAbstractor::translateFrom($from);
        $this->assertEquals($to, $translated);
    }

    public function contentsTo()
    {
        return array(
           array('Simple', 'Simple'),
           array('<p><a href="http://www.dummyco.com/path/to/server/index.php?cID=50">Test</a></p>', '<p><a href="{CCM:CID_50}">Test</a></p>'),
           array('<p><a href="http://www.dummyco.com/path/to/server/">Test</a></p>', '<p><a href="{CCM:BASE_URL}/">Test</a></p>'),
           array('Test<img src="http://www.dummyco.com/path/to/server/index.php/download_file/view_inline/1">', 'Test<concrete-picture fID="1" />'),
           array('Test<img src="http://www.dummyco.com/path/to/server/index.php/download_file/view_inline/1" alt="Woohoo" style="display: block" />', 'Test<concrete-picture fID="1" alt="Woohoo" style="display: block" />'),
           array('<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/1">Test</a>', '<a href="{CCM:FID_DL_1}">Test</a>'),
        );
    }

    public function contentsFromEditMode()
    {
        return array(
            array('Simple', 'Simple'),
            array('<p><a href="http://www.dummyco.com/path/to/server/index.php?cID=50">Test</a></p>', '<p><a href="{CCM:CID_50}">Test</a></p>'),
            array('Test<img src="http://www.dummyco.com/path/to/server/index.php/download_file/view_inline/1" alt="Woohoo" style="display: block" />', 'Test<concrete-picture fID="1" alt="Woohoo" style="display: block" />'),
            array('<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/1">Test</a>', '<a href="{CCM:FID_DL_1}">Test</a>'),
            array('<p><a href="http://www.dummyco.com/path/to/server">Test</a></p>', '<p><a href="{CCM:BASE_URL}">Test</a></p>'),
        );
    }

    public function contentsFrom()
    {
        return array(
            array('Simple', 'Simple'),
            array('<p>Super super super {CCM:BASE_URL} !!</p>', '<p>Super super super http://www.dummyco.com/path/to/server !!</p>'),
            array('<p><a href="{CCM:FID_DL_8}">Download File</a></p>', '<p><a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/8">Download File</a></p>'),
        );
    }
}
