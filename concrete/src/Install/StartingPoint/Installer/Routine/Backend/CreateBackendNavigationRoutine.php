<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Backend;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class CreateBackendNavigationRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Creating Backend Navigation');
    }


}
