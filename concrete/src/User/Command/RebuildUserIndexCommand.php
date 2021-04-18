<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\Command\RebuildIndexCommandHandler;
use Concrete\Core\Page\Command\AbstractRebuildIndexCommand;

class RebuildUserIndexCommand extends AbstractRebuildIndexCommand
{

    public function getAttributeKeyCategory(): CategoryInterface
    {
        return app(UserCategory::class);
    }

    public static function getHandler(): string
    {
        return RebuildIndexCommandHandler::class;
    }

    public function getIndexName()
    {
        return tc('IndexName', 'Users');
    }
}
