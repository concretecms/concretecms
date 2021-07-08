<?php

namespace Concrete\Core\StyleCustomizer\Parser;

use Concrete\Core\StyleCustomizer\Parser\Normalizer\NormalizedVariableCollection;
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
class BedrockParser implements ThemeParserInterface
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

    /**
     * @param SkinInterface $skin
     * @return NormalizedVariableCollection
     */
    public function createVariableCollectionFromSkin(SkinInterface $skin): NormalizedVariableCollection
    {
        $variablesFile = $skin->getDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_SCSS .
            DIRECTORY_SEPARATOR .
            self::FILE_CUSTOMIZABLE_VARIABLES;

        $variableCollection = $this->normalizer->createVariableCollectionFromFile($variablesFile);
        return $variableCollection;
    }


}
