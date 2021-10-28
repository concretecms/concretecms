<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Compiler\Compiler;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Writer\Writer;

class GenerateCustomSkinStylesheetCommandHandler
{

    /**
     * @var StyleValueListFactory
     */
    protected $styleValueListFactory;

    /**
     * @var NormalizedVariableCollectionFactory
     */
    protected $variableCollectionFactory;

    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @param StyleValueListFactory $styleValueListFactory
     * @param NormalizedVariableCollectionFactory $variableCollectionFactory
     * @param Compiler $compiler
     */
    public function __construct(
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory,
        Compiler $compiler,
        Writer $writer
    ) {
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->compiler = $compiler;
        $this->writer = $writer;
    }

    public function __invoke(GenerateCustomSkinStylesheetCommand $command)
    {
        $skin = $command->getCustomSkin();
        $theme = Theme::getByID($command->getThemeID());
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier($skin->getPresetStartingPoint());
        $styleValueList = $this->styleValueListFactory->createFromVariableCollection(
            $customizer->getThemeCustomizableStyleList($preset),
            $skin->getVariableCollection()
        );
        $collection = $this->variableCollectionFactory->createFromStyleValueList($styleValueList);
        $result = $this->compiler->compileFromPreset($customizer, $preset, $collection);
        $result .= $skin->getCustomCss();
        $this->writer->writeStyles($skin, $result);
    }


}