<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class ImportStartingPointFilesRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Importing files.');
    }


}
