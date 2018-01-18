<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Set\Set;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161216100000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->installEntities(['Concrete\Core\Entity\Express\Entity']);
        $this->fixSerializedComposerControls();
    }

    public function down(Schema $schema)
    {
    }

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function installEntities($entities)
    {
        // Add tables for new entities or moved entities
        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');

        $em = $this->connection->getEntityManager();
        $cmf = $em->getMetadataFactory();
        $metadatas = [];
        $existingMetadata = $cmf->getAllMetadata();
        foreach ($existingMetadata as $meta) {
            if (in_array($meta->getName(), $entities)) {
                $this->output(t('Installing entity %s...', $meta->getName()));
                $metadatas[] = $meta;
            }
        }

        $sm->installDatabaseFor($metadatas);
    }

    protected function fixSerializedComposerControls()
    {
        $r = $this->connection->executeQuery('select ptComposerFormLayoutSetControlID from PageTypeComposerFormLayoutSetControls');
        while ($row = $r->fetch()) {
            $control = FormLayoutSetControl::getByID($row['ptComposerFormLayoutSetControlID']);
            $object = $control->getPageTypeComposerControlObject();
            $this->connection->executeQuery('update PageTypeComposerFormLayoutSetControls set ptComposerControlObject = ? where ptComposerFormLayoutSetControlID = ?', [serialize($object), $row['ptComposerFormLayoutSetControlID']]);
        }
    }
}
