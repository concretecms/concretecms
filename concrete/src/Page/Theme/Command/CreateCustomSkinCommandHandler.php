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

class CreateCustomSkinCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var User
     */
    protected $user;

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

    public function __construct(StyleValueListFactory $styleValueListFactory, NormalizedVariableCollectionFactory $variableCollectionFactory,
        EntityManager $entityManager, User $user, Application $app)
    {
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->app = $app;
    }

    public function __invoke(CreateCustomSkinCommand $command)
    {
        $author = null;
        if ($this->user->isRegistered()) {
            $author = $this->user->getUserInfoObject()->getEntityObject();
        }
        $text = new Text();
        $skin = new CustomSkin();
        $date = time();

        $theme = Theme::getByID($command->getThemeID());
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier($command->getPresetStartingPoint());

        $styleValueList = $this->styleValueListFactory->createFromRequestArray(
            $customizer->getThemeCustomizableStyleList($preset),
            $command->getStyles()
        );
        $collection = $this->variableCollectionFactory->createFromStyleValueList($styleValueList);

        $skin->setSkinName($command->getSkinName());
        $skin->setSkinIdentifier($text->urlify($command->getSkinName()));
        $skin->setVariableCollection($collection);
        $skin->setAuthor($author);
        $skin->setThemeID($command->getThemeID());
        $skin->setDateCreated($date);
        $skin->setDateUpdated($date);
        $skin->setPresetStartingPoint($command->getPresetStartingPoint());
        $skin->setCustomCss($command->getCustomCss());

        $this->entityManager->persist($skin);
        $this->entityManager->flush();

        $styleSheetCommand = new GenerateCustomSkinStylesheetCommand();
        $styleSheetCommand->setCustomSkin($skin);
        $styleSheetCommand->setThemeID($command->getThemeID());
        $this->app->executeCommand($styleSheetCommand);

        return $skin;
    }


}