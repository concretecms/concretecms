<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Compiler\Compiler;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Writer\Writer;

class GenerateCustomSkinStylesheetCommandHandler
{

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

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
     * @param AdapterFactory $adapterFactory
     * @param StyleValueListFactory $styleValueListFactory
     * @param NormalizedVariableCollectionFactory $variableCollectionFactory
     * @param Compiler $compiler
     */
    public function __construct(
        AdapterFactory $adapterFactory,
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory,
        Compiler $compiler,
        Writer $writer
    ) {
        $this->adapterFactory = $adapterFactory;
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->compiler = $compiler;
        $this->writer = $writer;
    }

    public function __invoke(GenerateCustomSkinStylesheetCommand $command)
    {
        $skin = $command->getCustomSkin();
        $theme = Theme::getByID($command->getThemeID());
        $presetSkin = $theme->getSkinByIdentifier($skin->getPresetSkinStartingPoint());
        $adapter = $this->adapterFactory->createFromTheme($theme);
        $styleValueList = $this->styleValueListFactory->createFromVariableCollection(
            $theme->getThemeCustomizableStyleList($skin),
            $skin->getVariableCollection()
        );
        $collection = $this->variableCollectionFactory->createFromStyleValueList($styleValueList);
        $result = $this->compiler->compileFromSkin($adapter, $presetSkin, $collection);
        $result .= $skin->getCustomCss();
        $this->writer->writeStyles($skin, $result);
    }


}