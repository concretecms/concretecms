<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20161216100000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->installEntities(['Concrete\Core\Entity\Express\Entity']);
        $this->fixSerializedComposerControls();
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
