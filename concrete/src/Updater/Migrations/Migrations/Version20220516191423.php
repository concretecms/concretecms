<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20220516191423 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $akc = Category::getByHandle('collection');
        $categoryController = $akc->getController();
        $attribute = $categoryController->getAttributeKeyByHandle('exclude_subpages_from_nav');
        if (!$attribute) {
            $key = $categoryController->createAttributeKey();
            $key->setAttributeKeyName('Exclude Subpages from Nav');
            $key->setAttributeKeyHandle('exclude_subpages_from_nav');
            $this->output(t('Attribute key exclude_subpages_from_nav not found. Creating...'));
            $type = Type::getByHandle('boolean');
            $categoryController->add($type, $key);
        }
        $attribute = $categoryController->getAttributeKeyByHandle('thumbnail');
        if (!$attribute) {
            $key = $categoryController->createAttributeKey();
            $key->setAttributeKeyName('Thumbnail');
            $key->setAttributeKeyHandle('thumbnail');
            $this->output(t('Attribute key thumbnail not found. Creating...'));
            $type = Type::getByHandle('image_file');
            $categoryController->add($type, $key);
        }
    }
}
