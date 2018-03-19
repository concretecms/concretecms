<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170412000000 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->connection->Execute('set foreign_key_checks = 0');
        $this->refreshEntities([
            'Concrete\Core\Entity\Attribute\Value\Value\AbstractValue',
            'Concrete\Core\Entity\Attribute\Value\Value\AddressValue',
            'Concrete\Core\Entity\Attribute\Value\Value\BooleanValue',
            'Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue',
            'Concrete\Core\Entity\Attribute\Value\Value\ExpressValue',
            'Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SelectValue',
            'Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue',
            'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
            'Concrete\Core\Entity\Attribute\Value\Value\TopicsValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\Attribute\Key\Key',
        ]);
        $this->connection->Execute('set foreign_key_checks = 1');
    }
}
