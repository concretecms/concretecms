<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class AddExpressObjectsSupportRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Creating results nodes and objects for Express.');
    }


}
