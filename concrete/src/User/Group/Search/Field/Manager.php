<?php

namespace Concrete\Core\User\Group\Search\Field;

use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Core\User\Group\Search\Field\Field\FolderField;
use Concrete\Core\User\Group\Search\Field\Field\NameField;

class Manager extends FieldManager
{
    public function __construct()
    {
        $this->addGroup(t('Core Properties'), [
            new NameField(),
            new FolderField()
        ]);
    }
}
