<?php

namespace Concrete\Tests\StyleCustomizer;

use Concrete\Core\Block\CustomStyle;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;

class InlineStyleSetTest extends ConcreteDatabaseTestCase
{
    /**
     * Importing a style set saves it.
     *
     * @var string[]
     */
    protected $entityClassNames = [
        'Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet',
    ];

    public function testPopulateFromRequestEmpty()
    {
        $r = Request::create('http://www.foo.com', 'POST', []);
        $set = StyleSet::populateFromRequest($r);
        $this->assertNull($set);
    }

    public function testPopulateFromRequestDefaults()
    {
        // These are all the arguments that could be set from a form post
        // where we SHOULDN'T createa  new set.
        $arguments = [
            'backgroundColor' => '',
            'backgroundImageFileID' => 0,
            'backgroundRepeat' => 'no-repeat',
            'linkColor' => '',
            'textColor' => '',
            'baseFontSize' => '0px',
            'marginTop' => '0px',
            'marginRight' => '0px',
            'marginBottom' => '0px',
            'marginLeft' => '0px',
            'paddingTop' => '0px',
            'paddingRight' => '0px',
            'paddingBottom' => '0px',
            'paddingLeft' => '0px',
            'borderWidth' => '0px',
            'borderColor' => '',
            'borderRadius' => '0px',
            'rotate' => '0',
            'boxShadowBlur' => '0px',
            'boxShadowColor' => '0px',
            'boxShadowHorizontal' => '0px',
            'boxShadowVertical' => '0px',
            'boxShadowSpread' => '0px',
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertNull($set);
    }

    public function testPopulateFromRequestSome()
    {
        $arguments = [
            'backgroundColor' => 'rgb(0,0,0)',
            'backgroundSize' => 'auto',
            'backgroundRepeat' => 'no-repeat',
            'backgroundPosition' => 'left top',
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $set);

        $b = new \Concrete\Core\Block\Block();
        $b->bID = 100;
        $a = new \Concrete\Core\Area\Area('Test');
        $b->setBlockAreaObject($a);
        $style = new CustomStyle($set, $b);
        $this->assertEquals('.ccm-custom-style-container.ccm-custom-style-test-100{background-color:rgb(0,0,0)}', $style->getCSS());

        $arguments = [
            'backgroundColor' => '',
            'backgroundImageFileID' => 10,
            'backgroundRepeat' => 'no-repeat',
            'backgroundPosition' => 'left top',
            'backgroundSize' => 'auto',
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $set);
    }

    public function testPopulateFromRequestBackgroundRepeat()
    {
        $arguments = [
            'textColor' => 'rgb(120,120,120)',
            'backgroundImageFileID' => 50,
            'backgroundRepeat' => 'no-repeat',
            'backgroundPosition' => 'left top',
            'backgroundSize' => 'auto',
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $set);
        $this->assertEquals('no-repeat', $set->getBackgroundRepeat());
        $this->assertEquals(50, $set->getBackgroundImageFileID());
        $this->assertEquals('rgb(120,120,120)', $set->getTextColor());
    }

    public function testPopulateFromRequestAll()
    {
        $arguments = [
            'backgroundColor' => 'rgb(0,0,0)',
            'backgroundImageFileID' => 50,
            'backgroundRepeat' => 'repeat-y',
            'linkColor' => 'rgb(20,20,20)',
            'textColor' => 'rgb(30,30,30)',
            'baseFontSize' => '15px',
            'marginTop' => '5px',
            'marginRight' => '10px',
            'marginBottom' => '15px',
            'marginLeft' => '20px',
            'paddingTop' => '25px',
            'paddingRight' => '30px',
            'paddingBottom' => '35px',
            'paddingLeft' => '40px',
            'borderWidth' => '3px',
            'borderStyle' => 'dotted',
            'borderColor' => 'rgb(40,40,40)',
            'borderRadius' => '4px',
            'alignment' => 'right',
            'rotate' => '-3',
            'boxShadowBlur' => '45px',
            'boxShadowColor' => 'rgb(50,50,50)',
            'boxShadowHorizontal' => '50px',
            'boxShadowVertical' => '55px',
            'boxShadowSpread' => '60px',
            'boxShadowInset' => '1',
            'customClass' => ['testclass'],
            'customID' => 'testid',
            'customElementAttribute' => 'data-test="test"',
            'backgroundSize' => 'auto',
            'backgroundPosition' => 'left top',
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $set);

        $this->assertEquals('rgb(0,0,0)', $set->getBackgroundColor());
        $this->assertEquals(50, $set->getBackgroundImageFileID());
        $this->assertEquals('repeat-y', $set->getBackgroundRepeat());
        $this->assertEquals('rgb(20,20,20)', $set->getLinkColor());
        $this->assertEquals('rgb(30,30,30)', $set->getTextColor());
        $this->assertEquals('15px', $set->getBaseFontSize());
        $this->assertEquals('5px', $set->getMarginTop());

        $this->assertEquals('10px', $set->getMarginRight());
        $this->assertEquals('15px', $set->getMarginBottom());
        $this->assertEquals('20px', $set->getMarginLeft());
        $this->assertEquals('25px', $set->getPaddingTop());
        $this->assertEquals('30px', $set->getPaddingRight());
        $this->assertEquals('35px', $set->getPaddingBottom());
        $this->assertEquals('40px', $set->getPaddingLeft());
        $this->assertEquals('3px', $set->getBorderWidth());
        $this->assertEquals('dotted', $set->getBorderStyle());
        $this->assertEquals('rgb(40,40,40)', $set->getBorderColor());
        $this->assertEquals('4px', $set->getBorderRadius());
        $this->assertEquals('right', $set->getAlignment());
        $this->assertEquals('-3', $set->getRotate());
        $this->assertEquals('45px', $set->getBoxShadowBlur());
        $this->assertEquals('rgb(50,50,50)', $set->getBoxShadowColor());
        $this->assertEquals('50px', $set->getBoxShadowHorizontal());
        $this->assertEquals('55px', $set->getBoxShadowVertical());
        $this->assertEquals('60px', $set->getBoxShadowSpread());
        $this->assertTrue($set->getBoxShadowInset());
        $this->assertEquals('testclass', $set->getCustomClass());
        $this->assertEquals('testid', $set->getCustomID());
        $this->assertEquals('data-test="test"', $set->getCustomElementAttribute());
    }

    public function testAllowedCssClasses()
    {
        $arguments = [
            'customClass' => ['testclass', 'md:text-left sm:text-right', 'something-special mt-5 pb-5'],
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $set);

        $this->assertEquals('testclass md:text-left sm:text-right something-special mt-5 pb-5', $set->getCustomClass());
    }

    public function testDisallowedCssClasses()
    {
        $arguments = [
            'customClass' => ['allowed-one', 'md:two', '"><script>alert(\'uhoh\')</script>', 'ok-one', 'md-5', '<nothere>', 'nothing'],
        ];
        $r = Request::create('http://www.foo.com', 'POST', $arguments);
        $set = StyleSet::populateFromRequest($r);
        $this->assertInstanceOf('\Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet', $set);

        $this->assertEquals('allowed-one md:two ok-one md-5 nothing', $set->getCustomClass());
    }

    /**
     * A CIF is written by hand or by another site, and what it says is held to the same rules as a form.
     */
    public function testImportKeepsOnlyTheValuesThatHold()
    {
        $set = StyleSet::import(new SimpleXMLElement(<<<'EOT'
<style>
    <customClass>hero "evil" ok</customClass>
    <customID>   hero-1   </customID>
    <customElementAttribute>class="sneaky" data-x="1"</customElementAttribute>
    <backgroundColor>rgba(1,2,3,1)</backgroundColor>
    <backgroundRepeat>sometimes</backgroundRepeat>
    <backgroundSize>cover</backgroundSize>
    <borderStyle>painted</borderStyle>
    <borderWidth>-2px</borderWidth>
    <marginTop>10 miles</marginTop>
    <paddingTop>-5px</paddingTop>
    <alignment>sideways</alignment>
    <rotate>45deg</rotate>
</style>
EOT
        ));

        $this->assertGreaterThan(0, $set->getID());
        $this->assertSame('hero ok', $set->getCustomClass());
        $this->assertSame('hero-1', $set->getCustomID());
        $this->assertSame('data-x="1"', $set->getCustomElementAttribute());
        $this->assertSame('rgba(1,2,3,1)', $set->getBackgroundColor());
        $this->assertSame('cover', $set->getBackgroundSize());
        $this->assertSame('', $set->getBackgroundRepeat());
        $this->assertSame('', $set->getBorderStyle());
        $this->assertSame('', $set->getBorderWidth());
        $this->assertSame('', $set->getMarginTop());
        $this->assertSame('', $set->getPaddingTop());
        $this->assertSame('', $set->getAlignment());
        $this->assertSame('', $set->getRotate());
    }

    /**
     * The dialog refuses an attribute whose quotes are not closed, while a CIF is read for what holds in it:
     * the import goes on without it.
     */
    public function testImportDropsAnAttributeWhoseQuotesAreNotClosed()
    {
        $set = StyleSet::import(new SimpleXMLElement(<<<'EOT'
<style>
    <customElementAttribute>data-x="1</customElementAttribute>
    <textColor>#ff0000</textColor>
</style>
EOT
        ));

        $this->assertSame('', $set->getCustomElementAttribute());
        $this->assertSame('#ff0000', $set->getTextColor());
    }
}
