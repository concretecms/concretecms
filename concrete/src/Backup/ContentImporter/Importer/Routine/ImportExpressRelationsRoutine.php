<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportExpressRelationsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'express_relations';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        // Loop through all associations and set the related entities
        if (isset($sx->expressentities)) {
            foreach ($sx->expressentities->entity as $entityNode) {
                /**
                 * @var $entity \Concrete\Core\Entity\Express\Entity
                 */
                $entity = $em->find('Concrete\Core\Entity\Express\Entity', (string) $entityNode['id']);
                if (is_object($entity)) {
                    $default_view_form = $em->find('Concrete\Core\Entity\Express\Form', (string) $entityNode['default_view_form']);
                    if (is_object($default_view_form)) {
                        $entity->setDefaultViewForm($default_view_form);
                    }
                    $default_edit_form = $em->find('Concrete\Core\Entity\Express\Form', (string) $entityNode['default_edit_form']);
                    if (is_object($default_edit_form)) {
                        $entity->setDefaultEditForm($default_edit_form);
                    }
                }
                $em->persist($entity);
                if (isset($entityNode->associations)) {
                    foreach($entityNode->associations->association as $associationNode) {
                        /**
                         * @var $association \Concrete\Core\Entity\Express\Association
                         */
                        $association = $em->find('Concrete\Core\Entity\Express\Association', (string) $associationNode['id']);
                        if (is_object($association)) {
                            $source_entity = $em->find('Concrete\Core\Entity\Express\Entity', (string) $associationNode['source-entity']);
                            if (is_object($source_entity)) {
                                $association->setSourceEntity($source_entity);
                            }
                            $target_entity = $em->find('Concrete\Core\Entity\Express\Entity', (string) $associationNode['target-entity']);
                            if (is_object($target_entity)) {
                                $association->setTargetEntity($target_entity);
                            }
                        }
                        $em->persist($association);
                    }
                }
            }
        }
        $em->flush();
    }

}
