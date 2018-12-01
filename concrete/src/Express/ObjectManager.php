<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\AuthorControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Entity\Package;
use Concrete\Core\Error\ErrorList\Field\Field;
use Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator;
use Concrete\Core\Express\Generator\EntityHandleGenerator;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Express\Entry\Manager as EntryManager;
use Concrete\Core\Entity\Express\Entity as ExpressEntity;
use Concrete\Core\Express\Controller\Manager as ControllerManager;
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Component\HttpFoundation\Request;

class ObjectManager
{
    protected $entityManager;
    protected $app;
    /**
     * @var AttributeKeyHandleGenerator
     */
    protected $attributeKeyHandleGenerator;
    /**
     * @var EntityHandleGenerator
     */
    protected $entityHandleGenerator;


    public function __construct(EntityHandleGenerator $entityHandleGenerator, AttributeKeyHandleGenerator $attributeKeyHandleGenerator, Application $app, EntityManagerInterface $entityManager)
    {
        $this->app = $app;
        $this->attributeKeyHandleGenerator=$attributeKeyHandleGenerator;
        $this->entityHandleGenerator=$entityHandleGenerator;
        $this->entityManager = $entityManager;
    }

    public function getEntities($asObject = false)
    {
        $r = $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity');
        if ($asObject) {
            return $r;
        } else {
            return $r->findBy(['include_in_public_list' => true]);
        }
    }

    public function getList($entityHandle, $asObject = false)
    {
        $entity = $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByHandle($entityHandle);
        $list = new EntryList($entity);
        if ($asObject) {
            return $list;
        } else {
            return $list->getResults();
        }
    }

    public function refresh($object)
    {
        $this->entityManager->refresh($object);
        return $object;
    }

    public function buildObject($handle, $plural_handle, $name, Package $pkg = null)
    {
        $builder = $this->app->make(ObjectBuilder::class);
        $builder->createObject($name);
        $builder->setHandle($handle);
        $builder->setPluralHandle($plural_handle);
        if ($pkg) {
            $builder->setPackage($pkg);
        }
        return $builder;
    }


    public function duplicateObject(ExpressEntity $entity)
    {
        $clonedEntity=clone $entity;
        $newHandle=$this->entityHandleGenerator->generate($clonedEntity);
        $clonedEntity->setHandle($newHandle);
        $clonedEntity->setPluralHandle($newHandle."s");
        $attributeKeysMapping=[];// to know for every attribute key its duplicated key . usefull when duplicate forms
        foreach ($entity->getAttributes() as $attribute) {
            /**
             * @var $duplicatedKey ExpressKey
             */
            $duplicatedKey=clone $attribute;
            $duplicatedKey->setAttributeKeyID(null);
            $handle = $this->attributeKeyHandleGenerator->generate($duplicatedKey);
            $duplicatedKey->setEntity($clonedEntity);
            $duplicatedKey->setAttributeKeyHandle($handle);
            $clonedEntity->getAttributes()->add($duplicatedKey);
            //old key handle ==> duplicated Express key object
            $attributeKeysMapping[$attribute->getAttributeKeyHandle()]=$duplicatedKey;
        }
        //duplicate entity express node results
        $node = clone Node::getByID($entity->getEntityResultsNodeId());
        $duplicatedNode=$node->duplicate($node->getTreeNodeParentObject());
        $clonedEntity->setEntityResultsNodeId($duplicatedNode->getTreeNodeID());
        //duplicate entity association
        $mappingClonedAssociation=[];
        /**
         * @var $association Association
         */
        foreach ($entity->getAssociations() as $association) {
            /*** @var $clonedAssociation Association
             */
            $clonedAssociation=clone $association;
            $clonedAssociation->setSourceEntity($clonedEntity);
            $clonedEntity->getAssociations()->add($clonedAssociation);
            $mappingClonedAssociation[$association->getId()]=$clonedAssociation;
        }
        $duplicatedFormsMapping=[];
        //duplicate forms
        foreach ($entity->getForms() as $form) {
            /**
             * @var $form Form
             */
            $duplicatedForm=clone $form;
            $duplicatedForm->setEntity($clonedEntity);
            $clonedEntity->getForms()->add($duplicatedForm);
            $duplicatedForm->setFieldSets(new ArrayCollection());
            $duplicatedFormsMapping[$form->getId()]=$duplicatedForm;
            foreach ($form->getFieldSets() as $fieldSet) {
                //copy fieldset
                /**
                 * @var $fieldSet FieldSet
                 */
                $set = clone $fieldSet;
                $set->setForm($duplicatedForm);
                $duplicatedForm->getFieldSets()->add($set);
                /**
                 * @var $fieldSet FieldSet
                 */
                /**
                 * @var $control Control
                 */
                foreach ($fieldSet->getControls() as $control) {
                    $clonedControl=clone $control;
                    $clonedControl->setId((new UuidGenerator())->generate($this->entityManager, $clonedControl));
                    $clonedControl->setFieldSet($set);
                    if ($clonedControl instanceof AssociationControl && $control instanceof AssociationControl) {
                        $clonedControl->setAssociation($mappingClonedAssociation[$control->getAssociation()->getId()]);
                    } elseif ($clonedControl instanceof  AttributeKeyControl && $control instanceof AttributeKeyControl) {
                        $clonedControl->setAttributeKey($attributeKeysMapping[$control->getAttributeKey()->getAttributeKeyHandle()]);
                    }
                    $fieldSet->getControls()->add($clonedControl);
                }
            }
        }
        if (!empty($entity->getDefaultEditForm())) {
            $clonedEntity->setDefaultEditForm($duplicatedFormsMapping[$entity->getDefaultEditForm()->getId()]);
        }
        if (!empty($entity->getDefaultViewForm())) {
            $clonedEntity->setDefaultViewForm($duplicatedFormsMapping[$entity->getDefaultViewForm()->getId()]);
        }
        $this->entityManager->persist($clonedEntity);
        $this->entityManager->flush();
        return $clonedEntity;
    }

    public function buildEntry($entity)
    {
        $entity = is_string($entity) ? $this->getObjectByHandle($entity) : $entity;
        if ($entity instanceof ObjectBuilder) {
            $entity = $entity->getEntity();
        }
        $builder = $this->app->make(EntryBuilder::class);
        $builder->createEntry($entity);
        return $builder;
    }

    public function getEntry($entryID)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneBy(['exEntryID' => $entryID]);
    }

    public function deleteEntry($entryID)
    {
        $entry = $this->getEntry($entryID);
        if ($entry) {
            /**
             * @var $entry Entry
             */
            $entity = $entry->getEntity();
            if ($entity) {
                $request = Request::createFromGlobals();
                $controller = $this->getEntityController($entity);
                $manager = $controller->getEntryManager($request);
                $manager->deleteEntry($entry);
            }
        }
    }

    public function getObjectByID($entityID)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($entityID);
    }

    public function getObjectByHandle($entityHandle)
    {
        return $this->entityManager
            ->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneByHandle($entityHandle);
    }

    public function getEntityController(Entity $entity)
    {
        return $this->app->make(ControllerManager::class)->driver(
            $entity->getHandle()
        );
    }
}
