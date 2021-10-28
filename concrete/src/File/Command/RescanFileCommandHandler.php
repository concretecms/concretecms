<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\Rescanner;
use Doctrine\ORM\EntityManager;

class RescanFileCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Rescanner
     */
    protected $rescanner;

    public function __construct(Repository $config, EntityManager $em, Rescanner $rescanner)
    {
        $this->config = $config;
        $this->entityManager = $em;
        $this->rescanner = $rescanner;
    }

    public function __invoke(RescanFileCommand $command)
    {
        $f = $this->entityManager->find(FileEntity::class, $command->getFileID());
        if ($f) {
            $fv = $f->getApprovedVersion();
            if ($fv) {
                $this->rescanner->rescanFile($f);
            }
        }
    }


}