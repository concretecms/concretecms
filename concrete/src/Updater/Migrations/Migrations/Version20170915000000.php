<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170915000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $type = Type::getByHandle('select');
        $em = $this->connection->getEntityManager();
        if ($type) {
            /*
             * @var $type \Concrete\Core\Entity\Attribute\Type
             */
            $type->setAttributeTypeName('Option List');
            $em->persist($type);
            $em->flush();
        }

        $this->refreshEntities([
            'Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings',
        ]);
    }
}
