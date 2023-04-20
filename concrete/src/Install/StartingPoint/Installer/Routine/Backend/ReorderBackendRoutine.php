<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Backend;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class ReorderBackendRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Organizing Backend');
    }


}
