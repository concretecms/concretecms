<?php

class BlockStyleTest extends PageTestCase
{
    public function setUp()
    {
        $this->tables = array_merge($this->tables,
           array('StyleCustomizerInlineStyleSets', 'BlockTypes', 'Blocks', 'AttributeKeyCategories')
        );
        parent::setUp();
    }
    public function testPageStyles()
    {
        $ps = new \Concrete\Core\StyleCustomizer\Inline\StyleSet();
        $ps->setBackgroundColor('#ffffff');
        $ps->save();

        $psx = \Concrete\Core\StyleCustomizer\Inline\StyleSet::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\StyleCustomizer\Inline\StyleSet', $psx);
        $this->assertEquals(1, $psx->getID());
        $this->assertEquals('#ffffff', $psx->getBackgroundColor());
    }

    public function testPageStylesBlock()
    {
        $ps = new \Concrete\Core\StyleCustomizer\Inline\StyleSet();
        $ps->setBackgroundColor('#aaa');
        $ps->save();

        $c = $this->createPage('This is my test page');
        $bt = BlockType::installBlockType('content');
        $b = $c->addBlock($bt, 'Main', array('content' => 'Sample content.'));
        $b->setCustomStyleSet($ps);
        $this->assertEquals(1, $b->getCustomStyleSetID());

        $b2 = Block::getByID(1, $c, 'Main');
        $this->assertEquals(1, $b2->getBlockID());
        $style = $b2->getCustomStyle();
        $this->assertInstanceOf('\Concrete\Core\Block\CustomStyle', $style);

        $b2->resetCustomStyle();

        $css = $style->getCSS();
        $this->assertEquals('ccm-custom-style-container ccm-custom-style-main-1', $style->getContainerClass());
        $this->assertEquals('.ccm-custom-style-container.ccm-custom-style-main-1{background-color:#aaa}', $css);
    }
}
