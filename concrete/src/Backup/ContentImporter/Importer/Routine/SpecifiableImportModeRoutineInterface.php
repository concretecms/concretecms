<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter;

interface SpecifiableImportModeRoutineInterface
{

    /**
     * Either ContentImporter::IMPORT_MODE_INSTALL or ContentImporter::IMPORT_MODE_UPGRADE

     * @param string $importMode
     * @return mixed
     */
    function setImportMode(string $importMode): void;

}
