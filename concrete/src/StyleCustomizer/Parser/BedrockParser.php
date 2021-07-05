<?php

namespace Concrete\Core\StyleCustomizer\Parser;

use Concrete\Core\StyleCustomizer\Parser\Normalizer\ScssNormalizer;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\StyleValueList;
use Concrete\Core\StyleCustomizer\StyleList;

/**
 * The parser used for themes that adhere to the Bedrock theme building protocol
 *
 * Class BedrockParser
 * @package Concrete\Core\StyleCustomizer\Parser
 */
class BedrockParser implements ParserInterface
{
    const FILE_CUSTOMIZABLE_VARIABLES = '_customizable-variables.scss';

    /**
     * @var ScssNormalizer
     */
    protected $normalizer;

    public function __construct(ScssNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function createStyleValueListFromSkin(StyleList $styleList, SkinInterface $skin): StyleValueList
    {
        $variablesFile = $skin->getDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_SCSS .
            DIRECTORY_SEPARATOR .
            self::FILE_CUSTOMIZABLE_VARIABLES;

        $variableCollection = $this->normalizer->createVariableCollectionFromFile($variablesFile);
        $valueList = new StyleValueList();

        foreach ($styleList->getAllStyles() as $style) {
            $value = $style->createValueFromVariableCollection($variableCollection);
            if ($value) {
                $valueList->addValue($style, $value);
            }
        }

        return $valueList;
    }


}
