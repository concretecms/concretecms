<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Controller\Traits\DashboardExpressEntityTrait;
use Concrete\Core\Entity\Express\Entity;

/**
 * Deprecated. Just implement DashboardExpressEntityTrait instead.
 */
abstract class DashboardExpressEntityPageController extends DashboardSitePageController
{

    abstract public function getEntityName();

    use DashboardExpressEntityTrait;

    public function getExpressEntity(): Entity
    {
        return $this->entityManager->getRepository(Entity::class)->
            findOneByName($this->getEntityName());
    }
}
