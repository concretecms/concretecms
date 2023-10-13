<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class AddHomePageRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Creating the root home page.');
    }


}
