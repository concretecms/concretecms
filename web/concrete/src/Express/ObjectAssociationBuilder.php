<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Doctrine\ORM\EntityManagerInterface;

class ObjectAssociationBuilder
{

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    protected function addAssociation(Association $association, Entity $subject, Entity $target, $property = null)
    {
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setPropertyName($property);
        $subject->getAssociations()->add($association);
    }

    public function addManyToOne(Entity $subject, Entity $target, $property = null)
    {
        $this->addAssociation(new ManyToOneAssociation(),
            $subject, $target, $property);
    }

    public function addOneToMany(Entity $subject, Entity $target, $property = null)
    {
        $this->addAssociation(new OneToManyAssociation(),
            $subject, $target, $property);
    }

    public function addManyToMany(Entity $subject, Entity $target, $property = null)
    {
        $this->addAssociation(new ManyToManyAssociation(),
            $subject, $target, $property);
    }

    public function addOneToOne(Entity $subject, Entity $target, $property = null)
    {
        $this->addAssociation(new OneToOneAssociation(),
            $subject, $target, $property);
    }







}