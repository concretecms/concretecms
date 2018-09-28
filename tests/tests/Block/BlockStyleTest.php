<?php

namespace Concrete\Tests\Block;

use Block;
use BlockType;
use Concrete\TestHelpers\Page\PageTestCase;

class BlockStyleTest extends PageTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables,
           ['StyleCustomizerInlineStyleSets', 'Blocks', 'AttributeKeyCategories']
        );
        $this->metadatas = array_merge($this->metadatas,
            [
                'Concrete\Core\Entity\Page\Template',
                'Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet',
                'Concrete\Core\Entity\Block\BlockType\BlockType',
            ]
        );
    }

    public function testPageStyles()
    {
        $ps = new \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet();
        $ps->setBackgroundColor('#ffffff');
        $ps->save();

        $psx = \Concrete\Core\StyleCustomizer\Inline\StyleSet::getByID(1);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $psx);
        $this->assertEquals(1, $psx->getID());
        $this->assertEquals('#ffffff', $psx->getBackgroundColor());
    }

    public function testPageStylesBlock()
    {
        $ps = new \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet();
        $ps->setBackgroundColor('#aaa');
        $ps->save();

        $c = $this->createPage('This is my test page');
        $bt = BlockType::installBlockType('content');
        $b = $c->addBlock($bt, 'Main', ['content' => 'Sample content.']);
        $b->setCustomStyleSet($ps);
        $this->assertEquals($ps->getID(), $b->getCustomStyleSetID());

        $b2 = Block::getByID($b->getBlockID(), $c, 'Main');
        $this->assertEquals($b->getBlockID(), $b2->getBlockID());
        $style = $b2->getCustomStyle();
        $this->assertInstanceOf('\Concrete\Core\Block\CustomStyle', $style);

        $b2->resetCustomStyle();
        $id = $b->getBlockID();
        $css = $style->getCSS();

        $this->assertEquals(
            'ccm-custom-style-container ccm-custom-style-main-' . $id,
            $style->getContainerClass());
        $this->assertEquals(
            '.ccm-custom-style-container.ccm-custom-style-main-' . $id . '{background-color:#aaa}',
            $css);
    }
}
