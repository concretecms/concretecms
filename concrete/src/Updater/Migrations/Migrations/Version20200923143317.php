<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Entity\Design\DesignTag;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;


final class Version20200923143317 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            DesignTag::class,
            SlotTemplate::class,
            Template::class,
            CalendarEventConfiguration::class,
        ]);
    }
}
