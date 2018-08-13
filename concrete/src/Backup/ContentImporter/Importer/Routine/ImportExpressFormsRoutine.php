<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;
use Doctrine\ORM\Id\UuidGenerator;

class ImportExpressFormsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'express_forms';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();

        $em->getClassMetadata('Concrete\Core\Entity\Express\Form')->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        if (isset($sx->expressentities)) {
            foreach ($sx->expressentities->entity as $entityNode) {
                if (isset($entityNode->forms)) {
                    foreach($entityNode->forms->form as $formNode) {
                        $entity = $em->find('Concrete\Core\Entity\Express\Entity', (string) $entityNode['id']);
                        $form = $em->find('Concrete\Core\Entity\Express\Form', (string) $formNode['id']);
                        if (!is_object($form)) {
                            $form = new Form();
                            $form->setId((string) $formNode['id']);
                        }
                        $form->setName((string) $formNode['name']);
                        if (isset($formNode->fieldsets)) {
                            $fieldSetPosition = 0;
                            foreach($formNode->fieldsets->fieldset as $fieldSetNode) {
                                $fieldset = $em->find('Concrete\Core\Entity\Express\FieldSet', (string) $fieldSetNode['id']);
                                if (!$fieldset) {
                                    $fieldset = new FieldSet();
                                    $fieldset->setDescription((string)$fieldSetNode['description']);
                                    $fieldset->setTitle((string)$fieldSetNode['title']);
                                    $fieldset->setPosition($fieldSetPosition);
                                    $form->getFieldSets()->add($fieldset);
                                    $fieldset->setForm($form);
                                    $fieldSetPosition++;
                                }
                                if (isset($fieldSetNode->controls)) {
                                    $manager = \Core::make('express/control/type/manager');
                                    $controlPosition = 0;
                                    foreach($fieldSetNode->controls->control as $controlNode) {
                                        $control = $em->find('Concrete\Core\Entity\Express\Control\Control', (string) $controlNode['id']);
                                        if (!$control) {
                                            $type = $manager->driver((string) $controlNode['type']);
                                            $control = $type->getImporter()->import($controlNode, $entity);
                                            $control->setFieldSet($fieldset);
                                            $control->setPosition($controlPosition);
                                            $fieldset->getControls()->add($control);
                                            $controlPosition++;
                                        }
                                    }
                                }
                            }
                        }
                        $entity->getForms()->add($form);
                        $form->setEntity($entity);
                        $em->persist($entity);
                        $em->persist($form);
                    }
                }
            }
        }
        $em->flush();
        $em->getClassMetadata('Concrete\Core\Entity\Express\Form')->setIdGenerator(new UuidGenerator());
    }

}
