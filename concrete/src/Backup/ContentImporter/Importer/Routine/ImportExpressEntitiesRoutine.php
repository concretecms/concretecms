<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Permission\Category;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Validation\BannedWord\BannedWord;
use Doctrine\ORM\Id\UuidGenerator;

class ImportExpressEntitiesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'express_entities';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();

        $em->getClassMetadata('Concrete\Core\Entity\Express\Entity')->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        if (isset($sx->expressentities)) {
            foreach ($sx->expressentities->entity as $entityNode) {
                $entity = $em->find('Concrete\Core\Entity\Express\Entity', (string) $entityNode['id']);
                if (!is_object($entity)) {
                    $entity = new Entity();
                    $entity->setId((string) $entityNode['id']);
                }
                $entity->setPluralHandle((string) $entityNode['plural_handle']);
                $entity->setHandle((string) $entityNode['handle']);
                $entity->setDescription((string) $entityNode['description']);
                $entity->setName((string) $entityNode['name']);
                if (((string) $entityNode['include_in_public_list']) == '') {
                    $entity->setIncludeInPublicList(false);
                }
                $entity->setHandle((string) $entityNode['handle']);

                $tree = ExpressEntryResults::get();
                $node = $tree->getNodeByDisplayPath((string) $entityNode['results-folder']);
                $node = \Concrete\Core\Tree\Node\Type\ExpressEntryResults::add((string) $entityNode['name'], $node);
                $entity->setEntityResultsNodeId($node->getTreeNodeID());
                $em->persist($entity);

                // Import the attributes
                if (isset($entityNode->attributekeys)) {
                    $app = Facade::getFacadeApplication();
                    $category = new ExpressCategory($entity, $app, $em);
                    foreach($entityNode->attributekeys->attributekey as $keyNode) {
                        $type = $app->make('Concrete\Core\Attribute\TypeFactory')->getByHandle(
                            (string) $keyNode['type']
                        );
                        $category->import($type, $keyNode);
                    }
                }
            }
        }

        $em->flush();
        $em->getClassMetadata('Concrete\Core\Entity\Express\Entity')->setIdGenerator(new UuidGenerator());
    }

}
