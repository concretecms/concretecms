<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Attribute\Key\Settings\DurationSettings;
use Concrete\Core\Entity\Attribute\Value\Value\DurationValue;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20201231153200 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            DurationValue::class,
            DurationSettings::class
        ]);

        // Install the attribute type
        $type = Type::getByHandle('duration');

        if (!is_object($type)) {
            $type = Type::add('duration', 'Duration');

            foreach (['file', 'user', 'collection', 'site', 'event', 'site_type', 'express'] as $category) {
                $cat = Category::getByHandle($category);
                $cat->getController()->associateAttributeKeyType($type);
            }
        }
    }
}