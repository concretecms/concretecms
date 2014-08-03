<?php
class ContentTranslateTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();

    public function setUp()
    {
        $this->c = new \Concrete\Block\Content\Controller();
    }

    /**
     *  @dataProvider contentsTo
     */
    public function testTo($from, $to)
    {
        $translated = $this->c->translateTo($from);
        $this->assertEquals($to, $translated);
    }

    /**
     *  @dataProvider contentsFromEditMode
     */
    public function testFromEditMode($to, $from)
    {
        $translated = $this->c->translateFromEditMode($from);
        $this->assertEquals($to, $translated);
    }

    /**
     *  @dataProvider contentsFrom
     */
    public function testFrom($to, $from)
    {
        $translated = $this->c->translateFrom($from);
        $this->assertEquals($to, $translated);
    }

    public function contentsTo()
    {
        return array(
           array('Simple', 'Simple'),
           array('<p><a href="http://www.dummyco.com/index.php?cID=50">Test</a></p>', '<p><a href="{CCM:CID_50}">Test</a></p>'),
           array('<p><a href="http://www.dummyco.com">Test</a></p>', '<p><a href="{CCM:BASE_URL}">Test</a></p>'),
           array('Test<img src="/index.php/download_file/view_inline/1">', 'Test<img src="{CCM:FID_1}">'),
           array('<a href="/index.php/download_file/view/1">Test</a>', '<a href="{CCM:FID_DL_1}">Test</a>')
        );
    }

    public function contentsFromEditMode()
    {
        return array(
            array('Simple', 'Simple'),
            array('<p><a href="http://www.dummyco.com/index.php?cID=50">Test</a></p>', '<p><a href="{CCM:CID_50}">Test</a></p>'),
            array('Test<img src="/index.php/download_file/view_inline/1">', 'Test<img src="{CCM:FID_1}">'),
            array('<a href="/index.php/download_file/view/1">Test</a>', '<a href="{CCM:FID_DL_1}">Test</a>')
        );
    }

    public function contentsFrom()
    {
        return array(
            array('Simple', 'Simple'),
        );
    }

}