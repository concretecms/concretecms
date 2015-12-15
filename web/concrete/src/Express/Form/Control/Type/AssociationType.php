<?php

namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Control\SaveHandler\AssociationSaveHandler;
use Concrete\Core\Express\Form\Control\Type\Item\AssociationItem;
use Doctrine\ORM\EntityManager;

class AssociationType implements TypeInterface {

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $associations = $r->findBy(array('source_entity' => $entity), array('id' => 'asc'));
        $items = array();
        foreach($associations as $association) {
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
        return \Core::make('\Concrete\Core\Express\Form\Control\SaveHandler\AssociationSaveHandler');
    }




}