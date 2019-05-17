<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Express;

class ExpressEntity implements ObjectInterface
{
    /**
     * @var mixed
     */
    protected $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function getPermissionObject()
    {
        if (is_object($this->entity)) {
            $entity = $this->entity;
        } else {
            $entity = Express::getObjectByHandle($this->entity);
        }
        /**
         * @var $entity Entity
         */
        $nodeId = $entity->getEntityResultsNodeId();
        if ($nodeId) {
            $node = ExpressEntryResults::getByID($nodeId);
            return $node;
        } else {
            throw new \Exception(t('Unable to retrieve node ID for permission assignment.'));
        }
    }


}
