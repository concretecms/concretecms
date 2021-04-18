<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\Command\RebuildIndexCommandHandler;

class RebuildPageIndexCommand extends AbstractRebuildIndexCommand
{

    public function getAttributeKeyCategory(): CategoryInterface
    {
        return app(PageCategory::class);
    }

    public static function getHandler(): string
    {
        return RebuildIndexCommandHandler::class;
    }

    public function getIndexName()
    {
        return tc('IndexName', 'Pages');
    }

}
