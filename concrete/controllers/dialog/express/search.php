<?php

namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Search extends UserInterface
{
    protected $viewPath = '/dialogs/express/entry/search';
    /** @var EntityManagerInterface */
    protected $entityManager;

    protected function canAccess()
    {
        $entity = $this->getEntity();

        if ($entity instanceof Entity) {
            $permissionChecker = new Checker($entity);
            return $permissionChecker->canViewExpressEntries();
        }

        return false;
    }

    public function on_start()
    {
        parent::on_start();
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
    }

    /**
     * @return Entity|null
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function getEntity()
    {
        if ($this->request->query->has('exEntityID')) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            return $this->entityManager
                ->getRepository(Entity::class)
                ->findOneById($this->request->query->get('exEntityID'));
        }
    }

    public function entries()
    {
        $this->set('entity', $this->getEntity());
    }
}
