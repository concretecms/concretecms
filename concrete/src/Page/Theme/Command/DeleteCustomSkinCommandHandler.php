<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\StyleCustomizer\Writer\Writer;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;

class DeleteCustomSkinCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Writer
     */
    protected $writer;

    public function __construct(EntityManager $entityManager, Writer $writer)
    {
        $this->entityManager = $entityManager;
        $this->writer = $writer;
    }

    public function __invoke(DeleteCustomSkinCommand $command)
    {
        $skin = $command->getCustomSkin();

        $this->writer->clearStyles($skin);

        $this->entityManager->remove($skin);
        $this->entityManager->flush();

        return $skin;
    }


}