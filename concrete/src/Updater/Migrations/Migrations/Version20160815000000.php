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
        $this->installEntities([
            'Concrete\Core\Entity\Site\Type',
            'Concrete\Core\Entity\Site\Site',
        ]);

        $this->ensureSiteTypeExists();
    }

    public function down(Schema $schema)
    {

    }
}
