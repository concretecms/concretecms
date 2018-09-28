<?php
namespace Concrete\Core\Express;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Doctrine\ORM\EntityManagerInterface;

class ObjectAssociationBuilder
{

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

    public function addManyToOne(Entity $subject, Entity $target, $target_property = null, $inversed_by = null)
    {
        $target_property = $target_property ? $target_property : $target->getHandle();
        if ($inversed_by !== false) {
            $inversed_by = $inversed_by ? $inversed_by : $subject->getPluralHandle();
        }
        $this->addAssociation(new ManyToOneAssociation(),
            $subject, $target, $target_property, $inversed_by);

        if ($inversed_by) {
            $this->addAssociation(new OneToManyAssociation(),
                $target, $subject, $inversed_by, $target_property);

        }
    }

    public function addOneToMany(Entity $subject, Entity $target, $target_property = null, $inversed_by = null, $is_owning_association = null)
    {
        $target_property = $target_property ? $target_property : $target->getPluralHandle();
        if ($inversed_by !== false) {
            $inversed_by = $inversed_by ? $inversed_by : $subject->getHandle();
        }

        $this->addAssociation(new OneToManyAssociation(),
            $subject, $target, $target_property, $inversed_by, $is_owning_association);

        if ($inversed_by) {
            if (isset($is_owning_association) && $is_owning_association === true) {
                // That means the first association is the, so for this association we reset
                // the variable to a hard false
                $is_owning_association = false;
            }
            $this->addAssociation(new ManyToOneAssociation(),
                $target, $subject, $inversed_by, $target_property, $is_owning_association);
        }
    }

    public function addManyToMany(Entity $subject, Entity $target, $target_property = null, $inversed_by = null)
    {

        $target_property = $target_property ? $target_property : $target->getPluralHandle();
        $inversed_by = $inversed_by ? $inversed_by : $subject->getPluralHandle();

        $association = new ManyToManyAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_OWNING);
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($target_property);
        $association->setInversedByPropertyName($inversed_by);
        $subject->getAssociations()->add($association);

        $association = new ManyToManyAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_INVERSE);
        $association->setSourceEntity($target);
        $association->setTargetEntity($subject);
        $association->setTargetPropertyName($inversed_by);
        $association->setInversedByPropertyName($target_property);
        $target->getAssociations()->add($association);
    }

    public function addOneToOneUnidirectional(Entity $subject, Entity $target, $target_property = null)
    {
        $target_property = $target_property ? $target_property : $target->getHandle();
        $association = new OneToOneAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_OWNING);
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($target_property);
        $subject->getAssociations()->add($association);
    }

    public function addOneToOne(Entity $subject, Entity $target, $target_property = null, $inversed_by = null, $is_owning_association = null)
    {
        $target_property = $target_property ? $target_property : $target->getHandle();
        $inversed_by = $inversed_by ? $inversed_by : $subject->getHandle();

        $association = new OneToOneAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_OWNING);
        $association->setSourceEntity($subject);
        $association->setTargetEntity($target);
        $association->setTargetPropertyName($target_property);
        $association->setInversedByPropertyName($inversed_by);
        if ($is_owning_association) {
            $association->setIsOwningAssociation(true);
        }
        $subject->getAssociations()->add($association);

        $association = new OneToOneAssociation();
        $association->setAssociationType(ManyToManyAssociation::TYPE_INVERSE);
        $association->setSourceEntity($target);
        $association->setTargetEntity($subject);
        $association->setTargetPropertyName($inversed_by);
        $association->setInversedByPropertyName($target_property);
        if ($is_owning_association) {
            $association->setIsOwnedByAssociation(true);
        }
        $target->getAssociations()->add($association);
    }
}
