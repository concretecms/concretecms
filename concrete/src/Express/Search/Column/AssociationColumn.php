<?php
namespace Concrete\Core\Express\Search\Column;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Result\Result;

class AssociationColumn extends Column
{
    protected $association = false;
    protected $associationID;

    public function __construct(Association $association)
    {
        $this->association = $association;
        $this->associationID = $association->getId();
    }

    public function getColumnKey()
    {
        if (is_object($this->association)) {
            return 'association_' . $this->association->getId();
        }
    }

    public function getColumnName()
    {
        if (is_object($this->association)) {
            return $this->association->getTargetEntity()->getName();
        }
    }

    public function getAssociation()
    {
        return $this->association;
    }

    public function getColumnValue($obj)
    {
        if (is_object($this->association)) {
            $entryAssociation = $obj->getEntryAssociation($this->association);
            if ($entryAssociation) {
                $entries = [];
                foreach($entryAssociation->getSelectedEntries() as $entry) {
                    $entries[] = $entry->getLabel();
                }
                return implode('<br/>', $entries);
            }
        }
    }

    public function __sleep()
    {
        return ['associationID'];
    }

    /**
     * Initialize the instance once it has been deserialized.
     */
    public function __wakeup()
    {
        $em = \Database::connection()->getEntityManager();
        $this->association = $em->find('Concrete\Core\Entity\Express\Association', $this->associationID);
    }

}
