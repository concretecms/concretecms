<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceLog;
use Concrete\Core\Entity\Board\InstanceLogEntry;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20241217194138 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([Instance::class, InstanceLog::class, InstanceLogEntry::class]);
        $c = Page::getByPath('/dashboard/system/boards');
        if (is_object($c) && !$c->isError()) {
            $c->update(['cName' => 'Boards &amp; Summary']);
        }
        $this->createSinglePage('/dashboard/system/boards/summary_templates', 'Summary Templates');
    }
}
