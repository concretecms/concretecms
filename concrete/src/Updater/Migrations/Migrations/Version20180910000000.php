<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Entry\AssociationEntry;
use Concrete\Core\Entity\Express\Entry\ManyAssociation;
use Concrete\Core\Entity\Express\Entry\OneAssociation;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180910000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            OneAssociation::class,
            ManyAssociation::class,
            AssociationEntry::class,
            Entry::class,
            Entry\Association::class,
        ]);

        // Migrate data from old entries table to new
        if ($this->connection->tableExists('ExpressEntityAssociationSelectedEntries')) {
            $this->connection->transactional(function ($db) {
                $r = $db->query('select * from ExpressEntityAssociationSelectedEntries');
                while ($row = $r->fetch()) {
                    $db->insert('ExpressEntityAssociationEntries', [
                        'association_id' => $row['id'], 'exEntryID' => $row['exSelectedEntryID']
                    ]);
                }
                $this->connection->Execute('alter table ExpressEntityAssociationSelectedEntries rename _ExpressEntityAssociationSelectedEntries');
            });
        }

    }
}