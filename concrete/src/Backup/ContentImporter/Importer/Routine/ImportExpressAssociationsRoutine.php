<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;
use Doctrine\ORM\Id\UuidGenerator;

class ImportExpressAssociationsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'express_associations';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();

        $em->getClassMetadata('Concrete\Core\Entity\Express\Association')->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        if (isset($sx->expressentities)) {
            foreach ($sx->expressentities->entity as $entityNode) {
                if (isset($entityNode->associations)) {
                    foreach($entityNode->associations->association as $associationNode) {
                        $association = $em->find('Concrete\Core\Entity\Express\Association', (string) $associationNode['id']);
                        if (!is_object($association)) {
                            $class = '\\Concrete\\Core\Entity\\Express\\' . camelcase((string) $associationNode['type']) . 'Association';
                            $association = new $class();
                            $association->setId((string) $associationNode['id']);
                        }
                        /**
                         * @var $association \Concrete\Core\Entity\Express\Association
                         */
                        $association->setTargetPropertyName((string) $associationNode['target-property-name']);
                        $association->setInversedByPropertyName((string) $associationNode['inversed-by-property-name']);
                        $em->persist($association);
                    }
                }
            }
        }
        $em->flush();
        $em->getClassMetadata('Concrete\Core\Entity\Express\Association')->setIdGenerator(new UuidGenerator());
    }

}
