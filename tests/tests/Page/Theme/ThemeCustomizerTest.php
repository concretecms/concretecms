<?php

namespace Concrete\Tests\Page\Theme;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Adapter\ScssAdapter;
use Concrete\Core\StyleCustomizer\Style\CustomizerVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Normalizer\NumberVariable;
use Concrete\Core\StyleCustomizer\Normalizer\ScssNormalizer;
use Concrete\Core\StyleCustomizer\Normalizer\ScssNormalizerCompiler;
use Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Processor\ScssProcessor;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\FontFamilyStyle;
use Concrete\Core\StyleCustomizer\Style\SizeStyle;
use Concrete\Core\StyleCustomizer\Style\StyleValue;
use Concrete\Core\StyleCustomizer\Style\StyleValueList;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Style\TypeStyle;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\Style\Value\FontFamilyValue;
use Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Concrete\Core\StyleCustomizer\StyleList;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollection;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Concrete\Tests\TestCase;
use Concrete\Theme\Elemental\PageTheme;
use Illuminate\Filesystem\Filesystem;
use ScssPhp\ScssPhp\Compiler;

class ThemeCustomizerTest extends ConcreteDatabaseTestCase
{

    protected $metadatas = [
        'Concrete\Core\Entity\Page\Theme\CustomSkin',
    ];

    public function testIsThemeCustomizable()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $customizable = $theme->isThemeCustomizable();
        $this->assertTrue($customizable);
    }

    public function testGetThemeSkins()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skins = $theme->getPresetSkins();
        $this->assertCount(2, $skins);
    }

    public function testGetThemeDefaultSkin()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $defaultSkin = $theme->getThemeDefaultSkin();
        $this->assertInstanceOf(SkinInterface::class, $defaultSkin);
        $this->assertEquals('Default', $defaultSkin->getName());
        $this->assertEquals('default', $defaultSkin->getIdentifier());
    }

    public function testParserDetector()
    {
        $app = app();
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $fileLocator = new FileLocator(new Filesystem(), Facade::getFacadeApplication());
        $parserFactory = new AdapterFactory($app, $fileLocator);
        $parser = $parserFactory->createFromTheme($theme);
        $this->assertInstanceof(ScssAdapter::class, $parser);
    }

    public function testStyleListFromSkin()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skin = $theme->getSkinByIdentifier('default');
        $styleList = $theme->getThemeCustomizableStyleList($skin);
        $this->assertInstanceOf(StyleList::class, $styleList);
        $this->assertCount(3, $styleList->getSets());

        $allStyles = $styleList->getAllStyles();
        $this->assertIsIterable($allStyles);
        $style = $allStyles->current();
        $this->assertEquals('Primary Color', $style->getName());
        $this->assertCount(4, iterator_to_array($allStyles));
    }

    public function testStyleListTypeOptions()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skin = $theme->getSkinByIdentifier('default');
        $styleList = $theme->getThemeCustomizableStyleList($skin);
        $sets = $styleList->getSets();
        $set = $sets[1];
        $this->assertEquals('Header', $set->getName());
        $style = $set->getStyles()[0]; // should be logo font family
        $this->assertInstanceOf(TypeStyle::class, $style);
    }

    public function testVariableCollectionFromScss()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skin = $theme->getSkinByIdentifier('default');
        $styleList = $theme->getThemeCustomizableStyleList($skin);
        $scssNormalizer = new ScssNormalizer(new ScssNormalizerCompiler(), new Filesystem());
        $variablesFile = DIR_BASE_CORE .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEMES .
            DIRECTORY_SEPARATOR .
            'elemental' .
            DIRECTORY_SEPARATOR .
            DIRNAME_STYLE_CUSTOMIZER_SKINS .
            DIRECTORY_SEPARATOR .
            'default' .
            DIRECTORY_SEPARATOR .
            DIRNAME_SCSS .
            DIRECTORY_SEPARATOR .
            '_customizable-variables.scss';
        $variableCollection = $scssNormalizer->createVariableCollectionFromFile($variablesFile);
        $this->assertInstanceOf(NormalizedVariableCollection::class, $variableCollection);
        $this->assertCount(10, $variableCollection);
        $variable = $variableCollection->getValues()[0];
        $this->assertInstanceOf(Variable::class, $variable);

        $variables = [
            ['logo-color', '#75ca2a'],
            ['primary', '#75ca2a'],
            ['secondary', '#0099ff'],
            ['logo-font-family', 'Titillium Web']
        ];
        $numberVariables = [
            ['logo-font-size', '2.2', 'em'],
        ];

        foreach ($variables as $row) {
            $variable = $variableCollection->getVariable($row[0]);
            $this->assertInstanceOf(Variable::class, $variable);
            $this->assertEquals($row[1], $variable->getValue());
        }
        foreach ($numberVariables as $row) {
            $variable = $variableCollection->getVariable($row[0]);
            $this->assertInstanceOf(NumberVariable::class, $variable);
            $this->assertEquals($row[1], $variable->getNumber());
            $this->assertEquals($row[2], $variable->getUnit());
        }
    }


    public function testStyleListToVariableCollection()
    {
        $app = Facade::getFacadeApplication();
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $defaultSkin = $theme->getThemeDefaultSkin();
        $styleList = $theme->getThemeCustomizableStyleList($defaultSkin);
        $fileLocator = new FileLocator(new Filesystem(), Facade::getFacadeApplication());
        $adapterFactory = new AdapterFactory($app, $fileLocator);
        $styleValueListFactory = new StyleValueListFactory();
        $adapter = $adapterFactory->createFromTheme($theme);
        $serializer = app(JsonSerializer::class);
        $variableCollectionFactory = new NormalizedVariableCollectionFactory($serializer);
        $variableCollection = $variableCollectionFactory->createVariableCollectionFromSkin($adapter, $defaultSkin);

        $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);

        // Now that we have the style value list, let's go BACK to the variable collection
        // Why do we need this? Well, when we receive values from our form post in the customizer, we
        // transform that PHP array data into stylelist data using the previous test's methods. So now
        // we need to go from that styleList back to a variable collection, which we will then pass
        // to our scss compiler.
        $serializer = app(JsonSerializer::class);
        $variableCollectionFactory = new NormalizedVariableCollectionFactory($serializer);
        $variableCollection = $variableCollectionFactory->createFromStyleValueList($valueList);
        $this->assertInstanceOf(NormalizedVariableCollection::class, $variableCollection);
        $this->assertCount(10, $variableCollection);
        $variable = $variableCollection->getValues()[0];
        $this->assertInstanceOf(Variable::class, $variable);

        $variable = $variableCollection->get(1);
        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertEquals('secondary', $variable->getName());
        $this->assertEquals('rgba(0, 153, 255, 1)', $variable->getValue());
    }

    public function testScssProcessor()
    {
        $processor = new ScssProcessor(new Filesystem(), new Compiler());
        $variableCollection = new NormalizedVariableCollection();
        $file = dirname(__FILE__) . '/fixtures/test.scss';
        $css = $processor->compileFileToString($file, $variableCollection);
        $expected = <<<EOL
body {
  background-color: rgba(0, 0, 0, 0);
}

EOL;
        $this->assertEquals($expected, $css);
        $variableCollection->add(new Variable('background-color', 'rgba(255, 255, 0, 0.7)'));
        $css = $processor->compileFileToString($file, $variableCollection);
        $expected = <<<EOL
body {
  background-color: rgba(255, 255, 0, 0.7);
}

EOL;
        $this->assertEquals($expected, $css);
    }

    public function testCustomizerVariableCollection()
    {
        $app = app();
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $defaultSkin = $theme->getThemeDefaultSkin();
        $styleList = $theme->getThemeCustomizableStyleList($defaultSkin);
        $fileLocator = new FileLocator(new Filesystem(), Facade::getFacadeApplication());
        $adapterFactory = new AdapterFactory($app, $fileLocator);
        $styleValueListFactory = new StyleValueListFactory();
        $adapter = $adapterFactory->createFromTheme($theme);
        $serializer = app(JsonSerializer::class);
        $variableCollectionFactory = new NormalizedVariableCollectionFactory($serializer);
        $variableCollection = $variableCollectionFactory->createVariableCollectionFromSkin($adapter, $defaultSkin);
        $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
        $this->assertInstanceOf(StyleValueList::class, $valueList);
        $this->assertCount(4, $valueList->getValues());

        $styleValue = $valueList->getValues()[2];
        $this->assertInstanceOf(StyleValue::class, $styleValue);
        $value = $styleValue->getValue();
        $style = $styleValue->getStyle();
        $this->assertInstanceOf(TypeStyle::class, $style);
        $this->assertInstanceOf(TypeValue::class, $value);
        $this->assertEquals('Logo', $style->getName());
        $this->assertEquals('logo', $style->getVariable());

        $variableCollectionFactory = new CustomizerVariableCollectionFactory();
        $customizerVariableCollection = $variableCollectionFactory->createFromStyleValueList($valueList);
        $this->assertCount(10, $customizerVariableCollection->getValues());
    }



    public function testValueListFromRequestData()
    {
        $json = '[{"variable":"primary","value":{"r":117,"g":202,"b":42,"a":1}},{"variable":"secondary","value":{"r":0,"g":153,"b":255,"a":1}},{"variable":"logo-color","value":{"r":205,"g":107,"b":94,"a":1}},{"variable":"logo-font-family","value":{"fontFamily":"Helvetica"}}]';
        $styles = json_decode($json, true);
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skin = $theme->getSkinByIdentifier('default');
        $styleList = $theme->getThemeCustomizableStyleList($skin);
        $styleValueListFactory = new StyleValueListFactory();
        $valueList = $styleValueListFactory->createFromRequestArray($styleList, $styles);
        $this->assertInstanceOf(StyleValueList::class, $valueList);
        $this->assertCount(3, $valueList->getValues());
        $styleValue = $valueList->getValues()[1];
        $this->assertInstanceOf(StyleValue::class, $styleValue);
        $value = $styleValue->getValue();
        $style = $styleValue->getStyle();
        $this->assertInstanceOf(ColorStyle::class, $style);
        $this->assertInstanceOf(ColorValue::class, $value);
        $this->assertEquals('Secondary Color', $style->getName());
        $this->assertEquals('secondary', $style->getVariable());
        $this->assertEquals('0', $value->getRed());
        $this->assertEquals('153', $value->getGreen());
        $this->assertEquals('255', $value->getBlue());
        $this->assertEquals('1', $value->getAlpha());
    }

    public function testWebFontCollection()
    {
        $webFontCollectionFactory = app(WebFontCollectionFactory::class);
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skin = $theme->getSkinByIdentifier('night-road');
        $collection = $webFontCollectionFactory->createFromSkin($skin);
        $this->assertInstanceOf(WebFontCollection::class, $collection);
        $this->assertCount(3, $collection);
    }

    /*
    public static function tearDownAfterClass() {
        @unlink(dirname(__FILE__) . '/fixtures/testing.css');
        @unlink(dirname(__FILE__) . '/fixtures/cache/css/testing/styles.css');
        @rmdir(dirname(__FILE__) . '/fixtures/cache/css/testing');
        @rmdir(dirname(__FILE__) . '/fixtures/cache/css');
        @rmdir(dirname(__FILE__) . '/fixtures/cache');

        @unlink(DIR_PACKAGES . '/tester/themes/testerson/styles.css');
        @rmdir(DIR_PACKAGES . '/tester/themes/testerson');
        @rmdir(DIR_PACKAGES . '/tester/themes');
        @rmdir(DIR_PACKAGES . '/tester');
    }
    */

    /*
    public function testStyles()
    {
        $definition = DIR_TESTS . '/assets/Style/styles.xml';
        $styleList = \Concrete\Core\StyleCustomizer\StyleList::loadFromXMLFile($definition);
        $sets = $styleList->getSets();
        $styles = $sets[0]->getStyles();
        $styles2 = $sets[2]->getStyles();

        $this->assertTrue($styleList instanceof \Concrete\Core\StyleCustomizer\StyleList);
        $this->assertTrue(count($styleList->getSets()) == 3);
        $this->assertTrue($sets[2]->getName() == 'Spacing');
        $this->assertTrue($styles[0]->getVariable() == 'background-color');
        $this->assertTrue($styles[1]->getVariable() == 'top-header-bar-color');
        $this->assertTrue($styles[0]->getName() == 'Background');
        $this->assertTrue($styles[1]->getName() == 'Top Header Bar');

        $this->assertTrue($styles[0] instanceof \Concrete\Core\StyleCustomizer\Style\ColorStyle);
        $this->assertTrue($styles2[0] instanceof \Concrete\Core\StyleCustomizer\Style\SizeStyle);

        $this->assertTrue($styles[0]->getFormElementPath() == DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/color.php', sprintf('Incorrect path: %s', $styles[0]->getFormElementPath()));
        $this->assertTrue($styles2[0]->getFormElementPath() == DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/size.php', sprintf('Incorrect path: %s', $styles2[0]->getFormElementPath()));
    }

    public function testLessVariableColorParsing()
    {
        $defaults = DIR_TESTS . '/assets/Style/defaults.less';
        $list = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($defaults);

        $cs1 = new \Concrete\Core\StyleCustomizer\Style\ColorStyle();
        $cs1->setVariable('header-background');
        $cs2 = new \Concrete\Core\StyleCustomizer\Style\ColorStyle();
        $cs2->setVariable('header-nav');
        $cs3 = new \Concrete\Core\StyleCustomizer\Style\ColorStyle();
        $cs3->setVariable('body-font');
        $cs4 = new \Concrete\Core\StyleCustomizer\Style\ColorStyle();
        $cs4->setVariable('body-background');

        $value1 = $cs1->getValueFromList($list);
        $value2 = $cs2->getValueFromList($list);
        $value3 = $cs3->getValueFromList($list);
        $value4 = $cs4->getValueFromList($list);

        $this->assertTrue($value1->getRed() == 255 && $value1->getGreen() == 0 && $value1->getBlue() == 0 && $value1->hasAlpha() && $value1->getAlpha() == 0.5);
        $this->assertTrue($value2->getRed() == 238 && $value2->getGreen() == 238 && $value2->getBlue() == 238 && !$value2->hasAlpha());
        $this->assertTrue($value3->getRed() == 0 && $value3->getGreen() == 0 && $value3->getBlue() == 0 && $value3->hasAlpha());
        $this->assertTrue($value4->getRed() == 255 && $value4->getGreen() == 255 && $value4->getBlue() == 255 && !$value4->hasAlpha());
    }

    public function testLessVariableSizeParsing()
    {
        $defaults = DIR_TESTS . '/assets/Style/defaults.less';
        $list = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($defaults);

        $ss1 = new \Concrete\Core\StyleCustomizer\Style\SizeStyle();
        $ss1->setVariable('bottom-margin');
        $ss2 = new \Concrete\Core\StyleCustomizer\Style\SizeStyle();
        $ss2->setVariable('leading-paragraph-spacing');

        $value1 = $ss1->getValueFromList($list);
        $value2 = $ss2->getValueFromList($list);

        $this->assertTrue($value1->getSize() == '20' && $value1->getUnits() == 'px');
        $this->assertTrue($value2->getSize() == '1.5' && $value2->getUnits() == 'em');
    }

    public function testLessVariableFontFullParsing()
    {
        $defaults = DIR_TESTS . '/assets/Style/defaults.less';
        $list = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($defaults);

        $fs1 = new \Concrete\Core\StyleCustomizer\Style\TypeStyle();
        $fs1->setVariable('header-one');
        $value1 = $fs1->getValueFromList($list);
        $this->assertTrue($value1->getFontFamily() == 'Helvetica Neue');
        $this->assertTrue($value1->getFontWeight() == 'normal');
        $this->assertTrue($value1->getTextDecoration() == 'none');
        $this->assertTrue($value1->getTextTransform() == 'uppercase');
        $this->assertTrue($value1->getFontStyle() == 'italic');

        $this->assertTrue($value1->getColor() instanceof \Concrete\Core\StyleCustomizer\Style\Value\ColorValue);
        $c1 = $value1->getColor();
        $this->assertTrue($c1->getRed() == 51 && $c1->getGreen() == 51 && $c1->getBlue() == 51 && !$c1->hasAlpha());

        $this->assertTrue($value1->getFontSize() instanceof \Concrete\Core\StyleCustomizer\Style\Value\SizeValue);
        $this->assertTrue($value1->getLineHeight() instanceof \Concrete\Core\StyleCustomizer\Style\Value\SizeValue);
        $this->assertTrue($value1->getLetterSpacing() instanceof \Concrete\Core\StyleCustomizer\Style\Value\SizeValue);
        $s1 = $value1->getFontSize();
        $s2 = $value1->getLineHeight();
        $s3 = $value1->getLetterSpacing();
        $this->assertTrue($s1->getSize() == 16 && $s1->getUnits() == 'px');
        $this->assertTrue($s2->getSize() == 24 && $s2->getUnits() == 'px');
        $this->assertTrue($s3->getSize() == 0.5 && $s3->getUnits() == 'em');
    }

    public function testLessVariableFontPartialParsing()
    {
        $defaults = DIR_TESTS . '/assets/Style/defaults.less';
        $list = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($defaults);

        $ts = new \Concrete\Core\StyleCustomizer\Style\TypeStyle();
        $ts->setVariable('body');
        $value = $ts->getValueFromList($list);
        $this->assertTrue($value->getFontFamily() == 'Arial');
        $this->assertEquals(-1, $value->getFontWeight());
        $this->assertEquals(-1, $value->getTextDecoration());
        $this->assertEquals(-1, $value->getTextTransform());
    }

    public function testLessVariableImages()
    {
        $defaults = DIR_TESTS . '/assets/Style/defaults.less';
        $list = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($defaults);

        $ts = new \Concrete\Core\StyleCustomizer\Style\ImageStyle();
        $ts->setVariable('header-background');
        $value = $ts->getValueFromList($list);
        $this->assertTrue($value->getUrl() == 'images/logo.png');
    }
    */

    /*
    public function testCustomizableStyleSheetObjects()
    {
        $defaults = DIR_TESTS . '/assets/Style/elemental.less';
        $list = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($defaults);
        $env = Environment::get();

        $pt = new PageTheme();
        $pt->setThemeHandle('elemental');
        $pt->setThemeDirectory($env->getPath(DIRNAME_THEMES . '/elemental'));
        $pt->setThemeURL($env->getURL(DIRNAME_THEMES . '/elemental'));

        $sheets = $pt->getThemeCustomizableStyleSheets();
        $this->asserT(count($sheets) == 1);
        $this->assertTrue($sheets[0] instanceof \Concrete\Core\StyleCustomizer\Stylesheet);

        $css = $sheets[0]->getCss();
        $this->assertTrue(strpos($css, "background-image:url('/path/to/server/concrete/themes/elemental/images/background-slider-default.png')") !== false);

        $sheets[0]->setValueList($list);
        $css = $sheets[0]->getCss();
        $this->assertTrue(strpos($css, "background-image:url('/path/to/server/concrete/themes/elemental/images/testingit.jpg')") !== false);
        $this->assertTrue(strpos($css, 'font-family:"Testing Font Family"') !== false);

        $sheet = $pt->getStylesheetObject('typography.less');
        $sheet->setValueList($list);
        $this->assertTrue($sheet->getOutputPath() == DIR_BASE . '/application/files/cache/css/elemental/typography.css');
        $this->assertTrue($sheet->getOutputRelativePath() == DIR_REL . '/application/files/cache/css/elemental/typography.css');
    }*/
}
