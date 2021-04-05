<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\Command\RebuildIndexCommandHandler;
use Concrete\Core\Attribute\Command\RebuildIndexCommandInterface;
use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;

class RebuildUserIndexCommand implements RebuildIndexCommandInterface, HandlerAwareCommandInterface
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
