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

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $typeValue = new TypeValue();

        foreach ($this->getStyleTypes() as $styleType) {
            $style = app($styleType[0]);
            $style->setVariable($this->getVariable() . '-' . $styleType[1]);
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
