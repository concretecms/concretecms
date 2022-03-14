<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollection;

class TypeStyle extends Style
{

    const PARSE_SUBJECT_VARIABLE_COLLECTION = 'collection';
    const PARSE_SUBJECT_REQUEST = 'request';

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

    protected function parseStyleValueForVariable(string $variableName, string $parseType, $subject): TypeValue
    {
        $typeValue = new TypeValue();
        foreach ($this->getStyleTypes() as $styleType) {
            $style = app($styleType[0]);
            $style->setVariable($variableName . '-' . $styleType[1]);
            $style->setName($this->getName() . ' ' . $styleType[2]);
            if ($style instanceof FontFamilyStyle) {
                $style->setWebFonts($this->getWebFonts());
            }
            if ($parseType === self::PARSE_SUBJECT_VARIABLE_COLLECTION) {
                $value = $style->createValueFromVariableCollection($subject);
            } else if ($parseType === self::PARSE_SUBJECT_REQUEST) {
                $value = $style->createValueFromRequestDataCollection($subject);
            }
            if ($value) {
                $styleValue = new StyleValue($style, $value);
                $typeValue->addSubStyleValue($styleValue);
            }
        }
        return $typeValue;
    }

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $typeValue = $this->parseStyleValueForVariable($this->getVariableToInspect(), self::PARSE_SUBJECT_VARIABLE_COLLECTION, $collection);
        if ($typeValue->hasSubStyleValues()) {
            return $typeValue;
        }
        return null;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        $typeValue = $this->parseStyleValueForVariable($this->getVariable(), self::PARSE_SUBJECT_REQUEST, $styles);
        if (!$typeValue->hasSubStyleValues()) {
            // Legacy backward compatibility hack. See above
            $typeValue = $this->parseStyleValueForVariable($this->getVariable() . '-type', self:: PARSE_SUBJECT_REQUEST, $styles);
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['fonts'] = $this->getWebFonts();
        return $data;
    }

}
