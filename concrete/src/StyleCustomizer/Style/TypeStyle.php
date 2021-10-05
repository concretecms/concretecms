<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollection;

class TypeStyle extends Style
{

    use WebFontCollectionStyleTrait;

    private function getStyleTypes(): array
    {
        return [
            [ColorStyle::class, 'color', t('Color')],
            [FontFamilyStyle::class, 'font-family', t('Font Family')],
            [SizeStyle::class, 'font-size', t('Font Size')],
            [FontWeightStyle::class, 'font-weight', t('Font Weight')],
            [FontStyleStyle::class, 'font-style', t('Font Style')],
            [TextDecorationStyle::class, 'text-decoration', t('Text Decoration')],
            [TextTransformStyle::class, 'text-transform', t('Text Transform')],
        ];
    }

    protected function parseStyleValueForVariable(string $variableName, NormalizedVariableCollection $collection): TypeValue
    {
        $typeValue = new TypeValue();
        foreach ($this->getStyleTypes() as $styleType) {
            $style = app($styleType[0]);
            $style->setVariable($variableName . '-' . $styleType[1]);
            $style->setName($this->getName() . ' ' . $styleType[2]);
            if ($style instanceof FontFamilyStyle) {
                $style->setWebFonts($this->getWebFonts());
            }
            $value = $style->createValueFromVariableCollection($collection);
            if ($value) {
                $styleValue = new StyleValue($style, $value);
                $typeValue->addSubStyleValue($styleValue);
            }
        }
        return $typeValue;
    }

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $typeValue = $this->parseStyleValueForVariable($this->getVariable(), $collection);
        if (!$typeValue->hasSubStyleValues()) {
            // Legacy backward compatibility hack. The old customizer required that the "type" of the variable
            // be the variable suffix. So the `body` type variable is written as `body-type-color`, `body-type-font-size`, etc...
            // in the .less file. Let's check to see if this exists. Note to devs: you should NOT use this
            // convention going forward. Just name your variables the same in the .xml file and the .less/.sass
            // files. Note, this is only required on color, size, image, and type styles, because those are the
            // only types of variables available to the legacy customizer.
            $typeValue = $this->parseStyleValueForVariable($this->getVariable() . '-type', $collection);
        }
        if ($typeValue->hasSubStyleValues()) {
            return $typeValue;
        }
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        $typeValue = new TypeValue();
        foreach ($this->getStyleTypes() as $styleType) {
            $style = app($styleType[0]);
            $style->setVariable($this->getVariable() . '-' . $styleType[1]);
            $value = $style->createValueFromRequestDataCollection($styles);
            if ($value) {
                $styleValue = new StyleValue($style, $value);
                $typeValue->addSubStyleValue($styleValue);
            }
        }
        if ($typeValue->hasSubStyleValues()) {
            return $typeValue;
        }
        return null;
    }

    /**
     * @param TypeValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        throw new \RuntimeException(t('The TypeStyle class is a container style – it cannot convert to variables.'));
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['fonts'] = $this->getWebFonts();
        return $data;
    }

}
