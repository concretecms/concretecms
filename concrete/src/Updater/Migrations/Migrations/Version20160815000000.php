<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;

class Version20160815000000 extends AbstractMigration
{

    protected function installEntities($entities)
    {
        // Add tables for new entities or moved entities
        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');

        $em = $this->connection->getEntityManager();
        $cmf = $em->getMetadataFactory();
        $metadatas = array();
        $existingMetadata = $cmf->getAllMetadata();
        foreach($existingMetadata as $meta) {
            if (in_array($meta->getName(), $entities)) {
                $metadatas[] = $meta;
            }
        }

        $sm->installDatabaseFor($metadatas);
    }

    protected function ensureSiteTypeExists()
    {
        $service = \Core::make('site/type');
        $type = $service->getDefault();
        if (!is_object($type)) {
            $service->installDefault();
        }
    }


    public function up(Schema $schema)
    {
        // This is so stupid. We have to remove the foreign keys before the
        // install Entities function can recreate them because their type is
        // different now (GUID vs INT)
        $removeKeys = array(
            'ExpressAttributeKeys',
            'ExpressAttributeKeyTypes',
            'ExpressEntityAssociations',
            'ExpressFormFieldSetAssociationControls',
            'ExpressEntityEntryAssociations',
            'ExpressEntities',
            'ExpressEntityEntries',
            'ExpressFormFieldSets',
            'ExpressForms'
        );
        foreach($removeKeys as $tableName) {
            $table = $schema->getTable($tableName);
            foreach($table->getForeignKeys() as $key) {
                $table->removeForeignKey($key->getName());
            }
        }

        $db = \Database::get();
        $fromSchema = $this->connection->getSchemaManager()->createSchema();
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $schema);
        $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());

        $this->connection->beginTransaction();

        foreach ($saveQueries as $query) {
            $this->connection->executeQuery($query);
        }

        $this->connection->commit();

        $this->connection->beginTransaction();

        $this->connection->executeQuery('ALTER TABLE ExpressAttributeKeyTypes CHANGE exEntityID exEntityID CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressEntityAssociations CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE source_entity_id source_entity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE target_entity_id target_entity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressEntities CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE default_view_form_id default_view_form_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE default_edit_form_id default_edit_form_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressEntityEntries CHANGE exEntryEntityID exEntryEntityID CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressFormFieldSets CHANGE form_id form_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressForms CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE entity_id entity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressEntityEntryAssociations CHANGE association_id association_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->connection->executeQuery('ALTER TABLE ExpressFormFieldSetAssociationControls CHANGE association_id association_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');

        $this->connection->commit();


        $this->installEntities([
            'Concrete\Core\Entity\Site\Type',
            'Concrete\Core\Entity\Site\Site',
            'Concrete\Core\Entity\Attribute\Key\ExpressKey',
            'Concrete\Core\Entity\Express\Entity',
            'Concrete\Core\Entity\Express\Association',
            'Concrete\Core\Entity\Express\Entry\Association',
            'Concrete\Core\Entity\Express\FieldSet',
            'Concrete\Core\Entity\Express\Entry',
            'Concrete\Core\Entity\Express\Form',
            'Concrete\Core\Entity\Express\Control\AssociationControl',
            'Concrete\Core\Entity\Attribute\Key\Type\ExpressType',
        ]);

        $this->ensureSiteTypeExists();

        $blockTypes = [
            'express_entry_list',
            'express_entry_detail',
            'express_form'
            ];

        foreach($blockTypes as $btHandle) {
            $bt = \BlockType::getByHandle($btHandle);
            if (is_object($bt)) {
                $bt->refresh();
            }
        }
    }

    public function down(Schema $schema)
    {

    }
}
