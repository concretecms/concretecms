<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Text;
use Doctrine\ORM\EntityManager;

class UpdateCustomSkinCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var StyleValueListFactory
     */
    protected $styleValueListFactory;

    /**
     * @var NormalizedVariableCollectionFactory
     */
    protected $variableCollectionFactory;

    /**
     * @param EntityManager $entityManager
     * @param Application $app
     * @param StyleValueListFactory $styleValueListFactory
     * @param NormalizedVariableCollectionFactory $variableCollectionFactory
     */
    public function __construct(
        EntityManager $entityManager,
        Application $app,
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory
    ) {
        $this->entityManager = $entityManager;
        $this->app = $app;
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
    }


    public function __invoke(UpdateCustomSkinCommand $command)
    {
        $theme = $command->getCustomSkin()->getTheme();
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier($command->getCustomSkin()->getPresetStartingPoint());
        $styles = $command->getStyles();

        $styleValueList = $this->styleValueListFactory->createFromRequestArray(
            $customizer->getThemeCustomizableStyleList($preset),
            $styles
        );
        $collection = $this->variableCollectionFactory->createFromStyleValueList($styleValueList);

        $date = time();
        $skin = $command->getCustomSkin();
        $skin->setDateUpdated($date);
        $skin->setVariableCollection($collection);
        $skin->setCustomCss($command->getCustomCss());

        $this->entityManager->persist($skin);
        $this->entityManager->flush();

        $styleSheetCommand = new GenerateCustomSkinStylesheetCommand();
        $styleSheetCommand->setCustomSkin($skin);
        $styleSheetCommand->setThemeID($skin->getThemeID());
        $this->app->executeCommand($styleSheetCommand);

        return $skin;
    }


}