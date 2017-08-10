<?php
namespace Concrete\Core\Express;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Association\Applier;
use Concrete\Core\Express\Entry\Manager as EntryManager;
use Concrete\Core\Express\EntryBuilder\AssociationUpdater;
use Concrete\Core\Express\ObjectBuilder\AssociationBuilder;
use Doctrine\ORM\EntityManagerInterface;

class EntryBuilder
{

    protected $entryManager;
    protected $entity;
    protected $attributes = [];
    protected $associations = [];

    public function __construct(
        EntryManager $entryManager
    )
    {
        $this->entryManager = $entryManager;
    }

    public function createEntry(Entity $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function __call($nm, $a)
    {
        if (substr($nm, 0, 3) == 'set') {
            $nm = preg_replace('/(?!^)[[:upper:]]/', '_\0', $nm);
            $nm = strtolower($nm);
            $identifier = str_replace('set_', '', $nm);

            $association = $this->getEntity()->getAssociation($identifier);
            if ($association instanceof Association) {
                $this->associations[] = [$identifier, $a[0]];
            } else {
                $this->attributes[$identifier] = $a[0];
            }
        } else {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$nm.'()', E_USER_ERROR);
        }
        return $this;
    }

    public function save()
    {
        $entry = $this->entryManager->addEntry($this->getEntity());
        foreach($this->attributes as $key => $value) {
            $entry->setAttribute($key, $value);
        }
        $em = $this->entryManager->getEntityManager();
        $em->refresh($entry); // gotta repopulate that $attributes array on the Entry object.

        if (count($this->associations)) {
            foreach($this->associations as $row) {
                $associationHandle = $row[0];
                $subject = $row[1];
                $updater = new AssociationUpdater(new Applier($this->entryManager->getEntityManager()), $entry);
                $updater->associate($associationHandle, $subject);
            }
        }
        return $entry;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }


}
