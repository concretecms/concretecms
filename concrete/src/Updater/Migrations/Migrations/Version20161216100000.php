<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Site\Service;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\File;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\User\Group\Group;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\Support\Facade\Facade;

class Version20161216100000 extends AbstractMigration
{

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
        $metadatas = array();
        $existingMetadata = $cmf->getAllMetadata();
        foreach($existingMetadata as $meta) {
            if (in_array($meta->getName(), $entities)) {
                $this->output(t('Installing entity %s...', $meta->getName()));
                $metadatas[] = $meta;
            }
        }

        $sm->installDatabaseFor($metadatas);
    }

    public function up(Schema $schema)
    {
        $this->installEntities(array('Concrete\Core\Entity\Express\Entity'));
        $this->fixSerializedComposerControls();
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

    public function down(Schema $schema)
    {
    }
}
