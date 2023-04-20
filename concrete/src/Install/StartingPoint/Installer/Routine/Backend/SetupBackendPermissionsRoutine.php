<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Backend;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class SetupBackendPermissionsRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Setting permissions on backend.');
    }


}
