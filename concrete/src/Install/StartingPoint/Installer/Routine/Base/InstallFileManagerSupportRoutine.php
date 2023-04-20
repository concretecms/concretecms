<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class InstallFileManagerSupportRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Installing file manager supporting components.');
    }


}
