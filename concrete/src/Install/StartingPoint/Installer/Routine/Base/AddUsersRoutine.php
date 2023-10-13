<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class AddUsersRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Adding core users, groups and authentication.');
    }


}
