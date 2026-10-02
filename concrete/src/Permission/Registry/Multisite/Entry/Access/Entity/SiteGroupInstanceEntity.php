<?php
namespace Concrete\Core\Permission\Registry\Multisite\Entry\Access\Entity;

use Concrete\Core\Entity\Site\Site;

class SiteGroupInstanceEntity extends SiteGroupEntity
{

    protected $site;

    public function __construct(Site $site, $groupName)
    {
        parent::__construct($site->getType(), $groupName);
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface::getAccessEntity()
     *
     * @return \Concrete\Core\User\Group\Group|null the group of the site corresponding to the site group (NULL if the site group access entity type isn't installed, or if the group doesn't exist)
     */
    public function getAccessEntity()
    {
        $entity = parent::getAccessEntity();
        if (is_object($entity)) {
            return $entity->getInstanceGroup($this->site);
        }

        return null;
    }


}
