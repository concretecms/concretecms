<?php

namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Control\Type\Item\AssociationItem;
use Concrete\Core\Express\Form\Control\Validator\AssociationControlValidator;
use Doctrine\ORM\EntityManager;

class AssociationType implements TypeInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, EntityManager $entityManager)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    public function getType()
    {
        return 'association';
    }

    public function getValidator()
    {
        return $this->app->make(AssociationControlValidator::class);
    }

    public function getPluralDisplayName()
    {
        return t('Associations');
    }

    public function getDisplayName()
    {
        return t('Association');
    }

    public function getItems(Entity $entity)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Association');
        $associations = $r->findBy(['source_entity' => $entity], ['id' => 'asc']);
        $items = [];
        foreach ($associations as $association) {
            $item = new AssociationItem($association);
            $items[] = $item;
        }

        return $items;
    }

    public function createControlByIdentifier($id)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Association');
        $association = $r->findOneById($id);
        $control = new AssociationControl();
        $control->setAssociation($association);

        return $control;
    }

    public function getSaveHandler(Control $control)
    {
        return $control->getAssociation()->getSaveHandler();
    }

    public function getImporter()
    {
        return \Core::make('\Concrete\Core\Import\Item\Express\Control\AssociationControl');
    }
}
