<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Page\Theme\CustomSkin;
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

    public function __construct(EntityManager $entityManager, Application $app)
    {
        $this->entityManager = $entityManager;
        $this->app = $app;
    }

    public function handle(UpdateCustomSkinCommand $command)
    {
        $date = time();
        $skin = $command->getCustomSkin();
        $skin->setDateUpdated($date);
        $skin->setVariableCollection($command->getVariableCollection());
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