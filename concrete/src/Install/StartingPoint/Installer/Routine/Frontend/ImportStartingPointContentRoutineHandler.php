<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Traits\InstallOptionsAwareTrait;
use Concrete\Core\Install\StartingPoint\ThemeStartingPoint;
use Concrete\Core\Install\StartingPointService;
use Concrete\Core\Package\PackageService;
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

    /**
     * @var PackageService
     */
    protected $packageService;

    public function __construct(Filesystem $filesystem, ContentImporter $contentImporter, StartingPointService $startingPointService, PackageService $packageService)
    {
        $this->filesystem = $filesystem;
        $this->contentImporter = $contentImporter;
        $this->startingPointService = $startingPointService;
        $this->packageService = $packageService;
    }

    public function __invoke()
    {
        $handle = $this->installOptions->getStartingPointHandle();
        $startingPoint = $this->startingPointService->getByHandle($handle);
        $contentXmlFilename = FILENAME_CONTENT_XML;
        if ($startingPoint instanceof ThemeStartingPoint) {
            $package = $this->packageService->install($startingPoint->getPackage(), []);
            $contentXmlFilename = array_key_first($package->getContentSwapFiles());
        }
        $contentFile = $startingPoint->getDirectory() . DIRECTORY_SEPARATOR . $contentXmlFilename;
        if ($this->filesystem->isFile($contentFile)) {
            $this->contentImporter->importContentFile($contentFile);
        }
        if ($startingPoint instanceof ThemeStartingPoint) {
            if (method_exists($package, 'on_after_swap_content')) {
                $package->on_after_swap_content([]);
            }
        }
    }


}
