<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\PublicIdentifierControl;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\PublicIdentifierGenerator;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManager;

class Version20190509205043 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $generator = new PublicIdentifierGenerator();
        $this->refreshEntities([
            Entry::class,
            PublicIdentifierControl::class,
            Control::class,
        ]);

        // Now we have to populate this. Let's bust out of the entity manager for performance purposes.
        $connection = $this->connection;
        $this->connection->transactional(function() use ($connection, $generator) {
            $r = $connection->executeQuery('select exEntryID from ExpressEntityEntries where publicIdentifier is null');
            while ($row = $r->fetch()) {
                $identifier = $generator->generate();
                $connection->update(
                    'ExpressEntityEntries',
                    ['publicIdentifier' => $identifier],
                    ['exEntryID' => $row['exEntryID']]
                );
            }
        });
    }
}
