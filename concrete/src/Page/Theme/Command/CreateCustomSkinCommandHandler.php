<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Page\Theme\CustomSkin;
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

    public function __construct(EntityManager $entityManager, User $user, Application $app)
    {
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

        $skin->setSkinName($command->getSkinName());
        $skin->setSkinIdentifier($text->urlify($command->getSkinName()));
        $skin->setVariableCollection($command->getVariableCollection());
        $skin->setAuthor($author);
        $skin->setThemeID($command->getThemeID());
        $skin->setDateCreated($date);
        $skin->setDateUpdated($date);
        $skin->setPresetSkinStartingPoint($command->getPresetSkinStartingPoint());
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