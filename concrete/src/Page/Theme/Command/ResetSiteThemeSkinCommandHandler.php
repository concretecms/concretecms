<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Site\Service;
use Doctrine\ORM\EntityManager;

class ResetSiteThemeSkinCommandHandler
{

    /**
     * @var Service
     */
    protected $siteService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Service $siteService, EntityManager $entityManager)
    {
        $this->siteService = $siteService;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ResetSiteThemeSkinCommand $command)
    {
        $site = $command->getSite();
        $site->setThemeSkinIdentifier(null);
        $this->entityManager->persist($site);
        $this->entityManager->flush();
    }


}