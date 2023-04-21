<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Frontend;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class ImportStartingPointContentRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Importing content.');
    }


}
