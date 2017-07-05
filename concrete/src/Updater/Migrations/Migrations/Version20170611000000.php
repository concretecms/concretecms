<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\File\File;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170611000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\Attribute\Key\Settings\BooleanSettings',
        ]);
        $em = $this->connection->getEntityManager();
        $ak = CollectionKey::getByHandle('exclude_nav');
        if ($ak) {
            $settings = $ak->getController()->getAttributeKeySettings();
            if ($settings) {
                $settings->setCheckboxLabel('Exclude Page from Navigation');
                $settings->setAttributeKey($ak);
                $em->persist($settings);
            }
        }
        $ak = CollectionKey::getByHandle('exclude_page_list');
        if ($ak) {
            $settings = $ak->getController()->getAttributeKeySettings();
            if ($settings) {
                $settings->setCheckboxLabel('Exclude Page from Page List Blocks');
                $settings->setAttributeKey($ak);
                $em->persist($settings);
            }
        }
        $ak = CollectionKey::getByHandle('is_featured');
        if ($ak) {
            $settings = $ak->getController()->getAttributeKeySettings();
            if ($settings) {
                $settings->setCheckboxLabel('Feature this Page');
                $settings->setAttributeKey($ak);
                $em->persist($settings);
            }
        }
        $em->flush();
    }

    public function down(Schema $schema)
    {
    }
}
