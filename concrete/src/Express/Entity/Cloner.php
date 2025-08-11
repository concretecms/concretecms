<?php

namespace Concrete\Core\Express\Entity;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry\Association;
use Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator;
use Concrete\Core\Express\Generator\EntityHandleGenerator;
use Concrete\Core\Tree\Node\Node;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

/**
 * Class to clone express entities.
 */
class Cloner implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var EntityHandleGenerator
     */
    private $entityHandleGenerator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Cloner constructor.
     * @param EntityHandleGenerator $entityHandleGenerator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityHandleGenerator $entityHandleGenerator, EntityManagerInterface $entityManager)
    {
        $this->entityHandleGenerator = $entityHandleGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * Duplicate Express Entity and return the newly created Entity.
     *
     * @param Entity $entity to be cloned
     * @param array<string, string> &$controlsMapping (Optional) Map old form controls ID's to new ones [oldFormControlID => newFormControlID]
     *
     * @return Entity cloned entity
     */
    public function cloneEntity(Entity $entity, array &$controlsMapping = []): Entity
    {
        $newEntity = clone $entity;
        $newEntity->setHandle($this->entityHandleGenerator->generate($newEntity));
        $node = clone Node::getByID($entity->getEntityResultsNodeId());
        $newNode = $node->duplicate($node->getTreeNodeParentObject());
        $newEntity->setEntityResultsNodeId($newNode->getTreeNodeID());
        $this->entityManager->persist($newEntity);
        $this->entityManager->flush();

        $akMapping = $this->cloneEntityAttributes($entity, $newEntity);
        $associationMapping = $this->cloneEntityAssociations($entity, $newEntity);
        $this->cloneEntityForms($entity, $newEntity, $akMapping, $associationMapping, $controlsMapping);

        return $newEntity;
    }

    /**
     * Clone Entity Attributes
     *
     * @param Entity $sourceEntity
     * @param Entity $destinationEntity
     *
     * @return array<string, ExpressKey> Return attribute key handle mapping [sourceKeyHandle => $newKey]
     */
    protected function cloneEntityAttributes(Entity $sourceEntity, Entity $destinationEntity): array
    {
        $akMapping = [];
        $attributeKeyHandleGenerator = new AttributeKeyHandleGenerator(
            $this->app->make(ExpressCategory::class, ['entity' => $destinationEntity])
        );
        foreach ($sourceEntity->getAttributes() as $ak) {
            $newKey = clone $ak;
            $newKey->setAttributeKeyID(null);
            $newKey->setAttributeKeyHandle($attributeKeyHandleGenerator->generate($newKey));
            $this->entityManager->persist($newKey);

            $newKey->setEntity($destinationEntity);
            $destinationEntity->getAttributes()->add($newKey);
            $akMapping[$ak->getAttributeKeyHandle()] = $newKey;
        }

        $this->entityManager->flush();

        // Import Attribute Key Settings
        foreach ($sourceEntity->getAttributes() as $ak) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $akSettingsXml = new SimpleXMLElement(
                '<?xml version="1.0" encoding="UTF-8"?><root/>',
                LIBXML_BIGLINES | LIBXML_COMPACT
            );

            $ak->getController()->exportKey($akSettingsXml);
            $newKey = $akMapping[$ak->getAttributeKeyHandle()];
            $newSettings = $newKey->getController()->importKey($akSettingsXml);
            if ($newSettings === null) {
                $newSettings = $newKey->getController()->getAttributeKeySettings();
            }

            $newSettings->setAttributeKey($newKey);
            $newKey->setAttributeKeySettings($newSettings);
            $this->entityManager->persist($newSettings);
        }

        $this->entityManager->flush();

        return $akMapping;
    }

    /**
     * Clone Entity Associations
     *
     * @param Entity $sourceEntity
     * @param Entity $destinationEntity
     *
     * @return array<string, Association> Return association mapping [sourceAssociationId => newAssociation]
     */
    protected function cloneEntityAssociations(Entity $sourceEntity, Entity $destinationEntity): array
    {
        $associationMapping = [];
        foreach ($sourceEntity->getAssociations() as $association) {
            $newAssociation = clone $association;
            $this->entityManager->persist($newAssociation);

            $newAssociation->setSourceEntity($destinationEntity);
            $destinationEntity->getAssociations()->add($newAssociation);
            $associationMapping[$association->getId()] = $newAssociation;
        }

        $this->entityManager->flush();

        return $associationMapping;
    }

    /**
     * Clone Entity Forms
     *
     * @param Entity $sourceEntity
     * @param Entity $destinationEntity
     * @param array<string, ExpressKey> $akMapping attribute key mapping [sourceKeyHandle => newKey]
     * @param array<string, Association> $associationMapping attribute key mapping [sourceAssociationId => newAssociation]
     * @param array<string, string> &$controlsMapping (Optional) Map old form controls ID's to new ones [oldFormControlID => newFormControlID]
     */
    protected function cloneEntityForms(Entity $sourceEntity, Entity $destinationEntity, array $akMapping, array $associationMapping, array &$controlsMapping = []): void
    {
        $formsMapping = [];
        foreach ($sourceEntity->getForms() as $form) {
            $newForm = clone $form;
            $this->entityManager->persist($newForm);
            $newForm->setEntity($destinationEntity);
            $destinationEntity->getForms()->add($newForm);
            $formsMapping[$form->getId()] = $newForm;

            foreach ($form->getFieldSets() as $fieldSet) {
                $newFieldSet = clone $fieldSet;
                $this->entityManager->persist($newFieldSet);

                $newFieldSet->setForm($newForm);
                $newForm->getFieldSets()->add($newFieldSet);

                foreach ($fieldSet->getControls() as $control) {
                    $newControl = clone $control;
                    $newControl->setId(\uuid_create(UUID_TYPE_RANDOM));

                    if ($newControl instanceof AssociationControl && $control instanceof AssociationControl) {
                        $newControl->setAssociation($associationMapping[$control->getAssociation()->getId()]);
                    } elseif ($newControl instanceof AttributeKeyControl && $control instanceof AttributeKeyControl) {
                        $newControl->setAttributeKey($akMapping[$control->getAttributeKey()->getAttributeKeyHandle()]);
                    }

                    $newControl->setFieldSet($newFieldSet);
                    $newFieldSet->getControls()->add($newControl);
                    $this->entityManager->persist($newControl);

                    $controlsMapping[$control->getId()] = $newControl->getId();
                }
            }
        }

        if ($sourceEntity->getDefaultEditForm() !== null) {
            $destinationEntity->setDefaultEditForm($formsMapping[$sourceEntity->getDefaultEditForm()->getId()]);
        }

        if ($sourceEntity->getDefaultViewForm() !== null) {
            $destinationEntity->setDefaultViewForm($formsMapping[$sourceEntity->getDefaultViewForm()->getId()]);
        }

        $this->entityManager->flush();
    }
}
