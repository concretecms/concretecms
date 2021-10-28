<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Command\RebuildIndexCommandHandler;
use Concrete\Core\Page\Command\AbstractRebuildIndexCommand;

class RebuildFileIndexCommand extends AbstractRebuildIndexCommand
{

    public function getAttributeKeyCategory(): CategoryInterface
    {
        return app(FileCategory::class);
    }

    public static function getHandler(): string
    {
        return RebuildIndexCommandHandler::class;
    }

    public function getIndexName()
    {
        return tc('IndexName', 'Files');
    }
}
