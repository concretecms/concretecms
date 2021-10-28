<?php

namespace Concrete\Tests\Page\Theme;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Page\Theme\Color\ColorCollection;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Customizer\Customizer;
use Concrete\Core\StyleCustomizer\Customizer\Type\SkinCustomizerType;
use Concrete\Core\StyleCustomizer\Normalizer\ColorVariable;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Normalizer\NumberVariable;
use Concrete\Core\StyleCustomizer\Normalizer\ScssNormalizer;
use Concrete\Core\StyleCustomizer\Normalizer\ScssNormalizerCompiler;
use Concrete\Core\StyleCustomizer\Normalizer\Variable;
use Concrete\Core\StyleCustomizer\Preset\Preset;
use Concrete\Core\StyleCustomizer\Processor\ScssProcessor;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\CustomizerVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValue;
use Concrete\Core\StyleCustomizer\Style\StyleValueList;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\StyleList;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollection;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Concrete\Theme\Atomik\PageTheme;
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
        $theme->setThemeHandle('atomik');
        $customizable = $theme->isThemeCustomizable();
        $this->assertTrue($customizable);
    }

    public function testGetThemeSkins()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $this->assertTrue($theme->hasPresetSkins());
        $skins = $theme->getPresetSkins();
        $this->assertCount(2, $skins);
    }

    public function testGetElementalThemeSkins()
    {
        $theme = new \Concrete\Theme\Elemental\PageTheme();
        $theme->setThemeHandle('elemental');
        $this->assertFalse($theme->hasPresetSkins());
        $skins = $theme->getPresetSkins();
        $this->assertCount(0, $skins);
    }

    public function testGetThemeDefaultSkin()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $defaultSkin = $theme->getThemeDefaultSkin();
        $this->assertInstanceOf(SkinInterface::class, $defaultSkin);
        $this->assertEquals('Default', $defaultSkin->getName());
        $this->assertEquals('default', $defaultSkin->getIdentifier());
    }

    public function testGetThemeCustomizer()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $customizer = $theme->getThemeCustomizer();
        $this->assertInstanceOf(Customizer::class, $customizer);
        $type = $customizer->getType();
        $this->assertInstanceOf(SkinCustomizerType::class, $type);
        $this->assertEquals($type->getLanguage(), 'scss');
    }

    public function testGetThemeCustomizerPresets()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $customizer = $theme->getThemeCustomizer();
        $presets = $customizer->getPresets($theme);
        $this->assertCount(2, $presets);
        foreach (['Default', 'Rustic Elegance'] as $i => $presetName) {
            $this->assertEquals($presets[$i]->getName(), $presetName);
        }
    }

    public function testGetElementalCustomizerPresets()
    {
        $theme = new \Concrete\Theme\Elemental\PageTheme();
        $theme->setThemeHandle('elemental');
        $customizer = $theme->getThemeCustomizer();
        $presets = $customizer->getPresets($theme);
        $this->assertCount(4, $presets);
        foreach (['Blue Sky', 'Sunrise', 'Night Road', 'Royal'] as $i => $presetName) {
            $this->assertEquals($presets[$i]->getName(), $presetName);
        }
    }

    public function testStyleListFromSkin()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier('default');
        $this->assertInstanceOf(Preset::class, $preset);
        $styleList = $customizer->getThemeCustomizableStyleList($preset);
        $this->assertInstanceOf(StyleList::class, $styleList);
        $this->assertCount(7, $styleList->getSets());
        $allStyles = $styleList->getAllStyles();
        $this->assertIsIterable($allStyles);
        $style = $allStyles->current();
        $this->assertEquals('Primary', $style->getName());
        $this->assertCount(17, iterator_to_array($allStyles));
    }

    public function testStyleListTypeOptions()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier('default');
        $styleList = $customizer->getThemeCustomizableStyleList($preset);
        $sets = $styleList->getSets();
        $set = $sets[1];
        $this->assertEquals('Header', $set->getName());
        $style = $set->getStyles()[0]; // should be logo font family
        $this->assertInstanceOf(ColorStyle::class, $style);
    }

    public function testVariableCollectionFromScss()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier('default');
        $styleList = $customizer->getThemeCustomizableStyleList($preset);
        $scssNormalizer = new ScssNormalizer(new ScssNormalizerCompiler(), new Filesystem());
        $variablesFile = DIR_BASE_CORE .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEMES .
            DIRECTORY_SEPARATOR .
            'atomik' .
            DIRECTORY_SEPARATOR .
            'css' .
            DIRECTORY_SEPARATOR .
            DIRNAME_STYLE_CUSTOMIZER_PRESETS .
            DIRECTORY_SEPARATOR .
            'default' .
            DIRECTORY_SEPARATOR .
            '_customizable-variables.scss';
        $variableCollection = $scssNormalizer->createVariableCollectionFromFile($variablesFile);
        $this->assertInstanceOf(NormalizedVariableCollection::class, $variableCollection);
        $this->assertCount(17, $variableCollection);
        $variable = $variableCollection->getValues()[0];
        $this->assertInstanceOf(Variable::class, $variable);

        $variables = [
            ['primary', '#2D7AC0'],
            ['secondary', '#676D6F'],
        ];
        $numberVariables = [
            ['stripe-padding-y', '3', 'em'],
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

    public function testElementalVariableCollection()
    {
        $theme = Theme::getByFileHandle('elemental', DIR_FILES_THEMES_CORE);
        $customizer = $theme->getThemeCustomizer();
        $defaultPreset = $customizer->getPresetByIdentifier('defaults');
        $serializer = app(JsonSerializer::class);
        $variableCollectionFactory = new NormalizedVariableCollectionFactory($serializer);
        $variableCollection = $variableCollectionFactory->createFromPreset($customizer, $defaultPreset);

        $variables = [
            ['page-background-color', 'rgb(255, 255, 255)'],
            ['header-site-title-type-color', 'rgb(117, 202, 42)'],
        ];
        $numberVariables = [
            ['blockquote-left-padding-size', '60', 'px'],
        ];

        foreach ($variables as $row) {
            $variable = $variableCollection->getVariable($row[0]);
            $this->assertInstanceOf(ColorVariable::class, $variable);
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
        $theme = Theme::getByFileHandle('atomik', DIR_FILES_THEMES_CORE);
        $customizer = $theme->getThemeCustomizer();
        $defaultPreset = $customizer->getPresetByIdentifier('default');
        $styleList = $customizer->getThemeCustomizableStyleList($defaultPreset);
        $styleValueListFactory = new StyleValueListFactory();
        $serializer = app(JsonSerializer::class);
        $variableCollectionFactory = new NormalizedVariableCollectionFactory($serializer);
        $variableCollection = $variableCollectionFactory->createFromPreset($customizer, $defaultPreset);

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
        $this->assertCount(16, $variableCollection);
        $variable = $variableCollection->getValues()[0];
        $this->assertInstanceOf(Variable::class, $variable);

        $variable = $variableCollection->get(1);
        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertEquals('secondary', $variable->getName());
        $this->assertEquals('rgba(103, 109, 111, 1)', $variable->getValue());
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
        $theme = Theme::getByFileHandle('atomik', DIR_FILES_THEMES_CORE);
        $customizer = $theme->getThemeCustomizer();
        $defaultPreset = $customizer->getPresetByIdentifier('default');
        $styleList = $customizer->getThemeCustomizableStyleList($defaultPreset);
        $styleValueListFactory = new StyleValueListFactory();
        $serializer = app(JsonSerializer::class);
        $variableCollectionFactory = new NormalizedVariableCollectionFactory($serializer);
        $variableCollection = $variableCollectionFactory->createFromPreset($customizer, $defaultPreset);
        $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
        $this->assertInstanceOf(StyleValueList::class, $valueList);
        $this->assertCount(16, $valueList->getValues());

        $styleValue = $valueList->getValues()[2];
        $this->assertInstanceOf(StyleValue::class, $styleValue);
        $value = $styleValue->getValue();
        $style = $styleValue->getStyle();
        $this->assertInstanceOf(ColorStyle::class, $style);
        $this->assertInstanceOf(ColorValue::class, $value);
        $this->assertEquals('Light', $style->getName());
        $this->assertEquals('light', $style->getVariable());

        $variableCollectionFactory = new CustomizerVariableCollectionFactory();
        $customizerVariableCollection = $variableCollectionFactory->createFromStyleValueList($valueList);
        $this->assertCount(16, $customizerVariableCollection->getValues());
    }



    public function testValueListFromRequestData()
    {
        $json = '[{"variable":"primary","value":{"r":117,"g":202,"b":42,"a":1}},{"variable":"secondary","value":{"r":0,"g":153,"b":255,"a":1}},{"variable":"logo-color","value":{"r":205,"g":107,"b":94,"a":1}},{"variable":"logo-font-family","value":{"fontFamily":"Helvetica"}}]';
        $styles = json_decode($json, true);
        $theme = Theme::getByFileHandle('atomik', DIR_FILES_THEMES_CORE);
        $customizer = $theme->getThemeCustomizer();
        $defaultPreset = $customizer->getPresetByIdentifier('default');
        $styleList = $customizer->getThemeCustomizableStyleList($defaultPreset);
        $styleValueListFactory = new StyleValueListFactory();
        $valueList = $styleValueListFactory->createFromRequestArray($styleList, $styles);
        $this->assertInstanceOf(StyleValueList::class, $valueList);
        $this->assertCount(2, $valueList->getValues());
        $styleValue = $valueList->getValues()[1];
        $this->assertInstanceOf(StyleValue::class, $styleValue);
        $value = $styleValue->getValue();
        $style = $styleValue->getStyle();
        $this->assertInstanceOf(ColorStyle::class, $style);
        $this->assertInstanceOf(ColorValue::class, $value);
        $this->assertEquals('Secondary', $style->getName());
        $this->assertEquals('secondary', $style->getVariable());
        $this->assertEquals('0', $value->getRed());
        $this->assertEquals('153', $value->getGreen());
        $this->assertEquals('255', $value->getBlue());
        $this->assertEquals('1', $value->getAlpha());
    }

    public function testWebFontCollection()
    {
        $webFontCollectionFactory = app(WebFontCollectionFactory::class);
        $theme = Theme::getByFileHandle('atomik', DIR_FILES_THEMES_CORE);
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier('rustic-elegance');
        $collection = $webFontCollectionFactory->createFromPreset($preset);
        $this->assertInstanceOf(WebFontCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    public function testColorCollection()
    {
        $theme = new PageTheme();
        $theme->setThemeHandle('atomik');
        $collection = $theme->getColorCollection();
        $this->assertInstanceOf(ColorCollection::class, $collection);
        $this->assertCount(7, $collection);
    }


}
