<?php

namespace Concrete\Tests\Page\Theme;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Page\Theme\Color\ColorCollection;
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
        $this->assertCount(5, $styleList->getSets());

        $allStyles = $styleList->getAllStyles();
        $this->assertIsIterable($allStyles);
        $style = $allStyles->current();
        $this->assertEquals('Primary Color', $style->getName());
        $this->assertCount(14, iterator_to_array($allStyles));
    }

    public function testStyleListTypeOptions()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $skin = $theme->getSkinByIdentifier('default');
        $styleList = $theme->getThemeCustomizableStyleList($skin);
        $sets = $styleList->getSets();
        $set = $sets[1];
        $this->assertEquals('Typography', $set->getName());
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
        $this->assertCount(46, $variableCollection);
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
        $this->assertCount(41, $variableCollection);
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
        $this->assertCount(12, $valueList->getValues());

        $styleValue = $valueList->getValues()[2];
        $this->assertInstanceOf(StyleValue::class, $styleValue);
        $value = $styleValue->getValue();
        $style = $styleValue->getStyle();
        $this->assertInstanceOf(TypeStyle::class, $style);
        $this->assertInstanceOf(TypeValue::class, $value);
        $this->assertEquals('Heading 1', $style->getName());
        $this->assertEquals('h1', $style->getVariable());

        $variableCollectionFactory = new CustomizerVariableCollectionFactory();
        $customizerVariableCollection = $variableCollectionFactory->createFromStyleValueList($valueList);
        $this->assertCount(41, $customizerVariableCollection->getValues());
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

    public function testColorCollection()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('elemental');
        $collection = $theme->getColorCollection();
        $this->assertInstanceOf(ColorCollection::class, $collection);
        $this->assertCount(4, $collection);
    }


}
