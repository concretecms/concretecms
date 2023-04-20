<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine;

use Concrete\Core\Backup\ContentImporter;
use Illuminate\Filesystem\Filesystem;

class InstallFeatureContentRoutineHandler
{

    /**
     * @var ContentImporter
     */
    protected $contentImporter;

    public function __construct(ContentImporter $contentImporter)
    {
        $this->contentImporter = $contentImporter;
    }

    public function __invoke(InstallFeatureContentRoutine $routine)
    {
        $file = $routine->getContentFile();
        $this->contentImporter->importContentFile($file);
    }



}
