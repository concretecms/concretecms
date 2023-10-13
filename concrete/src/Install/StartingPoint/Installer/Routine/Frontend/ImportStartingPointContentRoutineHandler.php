<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Traits\InstallOptionsAwareTrait;
use Concrete\Core\Install\StartingPointService;
use Illuminate\Filesystem\Filesystem;

class ImportStartingPointContentRoutineHandler implements InstallOptionsAwareInterface
{

    use InstallOptionsAwareTrait;

    /**
     * @var StartingPointService
     */
    protected $startingPointService;

    /**
     * @var ContentImporter
     */
    protected $contentImporter;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem, ContentImporter $contentImporter, StartingPointService $startingPointService)
    {
        $this->filesystem = $filesystem;
        $this->contentImporter = $contentImporter;
        $this->startingPointService = $startingPointService;
    }

    public function __invoke()
    {
        $handle = $this->installOptions->getStartingPointHandle();
        $startingPoint = $this->startingPointService->getByHandle($handle);
        $contentFile = $startingPoint->getDirectory() . DIRECTORY_SEPARATOR . FILENAME_CONTENT_XML;
        if ($this->filesystem->isFile($contentFile)) {
            $this->contentImporter->importContentFile($contentFile);
        }
    }


}
