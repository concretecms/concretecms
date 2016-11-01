<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;

class ObjectAssociationBuilder
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    protected function addAssociation(Association $association, Entity $subject, Entity $target, $target_property = null, $inversed_by = null, $is_owning_association = null)
    {
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($target_property);
        $association->setInversedByPropertyName($inversed_by);
        if ($is_owning_association === true) {
            $association->setIsOwningAssociation(true);
        } else if ($is_owning_association === false) {
            $association->setIsOwnedByAssociation(true);
        }
        $subject->getAssociations()->add($association);
    }

    public function addManyToOne(Entity $subject, Entity $target, $subject_property = null, $inversed_by = null)
    {
        $this->addAssociation(new ManyToOneAssociation(),
            $subject, $target, $subject_property, $inversed_by);

        if ($inversed_by) {
            $this->addAssociation(new OneToManyAssociation(),
                $target, $subject, $inversed_by, $subject_property);

        }
    }

    public function addOneToMany(Entity $subject, Entity $target, $subject_property = null, $inversed_by = null, $is_owning_association = null)
    {
        $this->addAssociation(new OneToManyAssociation(),
            $subject, $target, $subject_property, $inversed_by, $is_owning_association);

        if ($inversed_by) {
            if (isset($is_owning_association) && $is_owning_association === true) {
                // That means the first association is the, so for this association we reset
                // the variable to a hard false
                $is_owning_association = false;
            }
            $this->addAssociation(new ManyToOneAssociation(),
                $target, $subject, $inversed_by, $subject_property, $is_owning_association);
        }
    }

    public function addManyToMany(Entity $subject, Entity $target, $subject_property = null, $target_property = null)
    {
        $association = new ManyToManyAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_OWNING);
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($target_property);
        $association->setInversedByPropertyName($subject_property);
        $subject->getAssociations()->add($association);

        $association = new ManyToManyAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_INVERSE);
        $association->setSourceEntity($target);
        $association->setTargetEntity($subject);
        $association->setTargetPropertyName($subject_property);
        $association->setInversedByPropertyName($target_property);
        $target->getAssociations()->add($association);
    }

    public function addOneToOneUnidirectional(Entity $subject, Entity $target, $subject_property = null, $inversed = null)
    {
        $association = new OneToOneAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_OWNING);
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($subject_property);
        $subject->getAssociations()->add($association);
    }

    public function addOneToOne(Entity $subject, Entity $target, $subject_property = null, $target_property = null, $is_owning_association = null)
    {
        $association = new OneToOneAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_OWNING);
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($target_property);
        $association->setInversedByPropertyName($subject_property);
        if ($is_owning_association) {
            $association->setIsOwningAssociation(true);
        }
        $subject->getAssociations()->add($association);

        $association = new OneToOneAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_INVERSE);
        $association->setSourceEntity($target);
        $association->setTargetEntity($subject);
        $association->setTargetPropertyName($subject_property);
        $association->setInversedByPropertyName($target_property);
        if ($is_owning_association) {
            $association->setIsOwnedByAssociation(true);
        }
        $target->getAssociations()->add($association);
    }
}
