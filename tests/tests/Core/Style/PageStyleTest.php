<?

class PageStyleTest extends PageTestCase {

     public function setUp()
    {
        parent::setUp();
        $this->tables = array_merge($this->tables,
           array('PageStyleSets', 'BlockTypes', 'Blocks')
        );
    }
    public function testPageStyles()
    {
        $ps = new \Concrete\Core\Page\Style\Set();
        $ps->setBackgroundColor('#ffffff');
        $ps->save();

        $psx = \Concrete\Core\Page\Style\Set::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\Page\Style\Set', $psx);
        $this->assertEquals(1, $psx->getID());
        $this->assertEquals('#ffffff', $psx->getBackgroundColor());
        $this->assertEquals('.ccm-custom-style-style-set-1{background-color:#ffffff}', $psx->getCSS());
    }

    public function testPageStylesBlock()
    {

        $ps = new \Concrete\Core\Page\Style\Set();
        $ps->setBackgroundColor('#aaa');
        $ps->save();

        $c = $this->createPage('This is my test page');
        $bt = BlockType::installBlockType('content');
        $b = $c->addBlock($bt, 'Main', array('content' => 'Sample content.'));
        $b->setCustomStyleSet($ps);
        $this->assertEquals(1, $b->getCustomStyleSetID());

        $b2 = Block::getByID(1, $c, 'Main');
        $this->assertEquals(1, $b2->getBlockID());
        $set = $b2->getCustomStyleSet();
        $this->assertInstanceOf('\Concrete\Core\Page\Style\Set', $set);

        $b2->resetCustomStyleSet();

        $css = $set->getCSS();
        $this->assertEquals('.ccm-custom-style-main-1{background-color:#aaa}', $css);
    }
}
