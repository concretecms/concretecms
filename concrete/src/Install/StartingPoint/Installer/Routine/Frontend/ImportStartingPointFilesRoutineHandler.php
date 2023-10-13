<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Traits\InstallOptionsAwareTrait;
use Concrete\Core\Install\StartingPointService;

class ImportStartingPointFilesRoutineHandler implements InstallOptionsAwareInterface
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

    public function __construct(ContentImporter $contentImporter, StartingPointService $startingPointService)
    {
        $this->contentImporter = $contentImporter;
        $this->startingPointService = $startingPointService;
    }

    public function __invoke()
    {
        $handle = $this->installOptions->getStartingPointHandle();
        $startingPoint = $this->startingPointService->getByHandle($handle);
        $filesDirectory = $startingPoint->getDirectory() . DIRECTORY_SEPARATOR . 'files';
        if (is_dir($filesDirectory)) {
            $computeThumbnails = true;
            if ($startingPoint->providesThumbnails()) {
                $computeThumbnails = false;
            }
            $this->contentImporter->importFiles($filesDirectory, $computeThumbnails);
        }
    }


}
