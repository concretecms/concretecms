<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\User\User;
use Doctrine\ORM\EntityRepository;

class NotificationAlertRepository extends EntityRepository
{
    /**
     * @return \Concrete\Core\Entity\Notification\NotificationAlert[]
     */
    public function findMyAlerts(User $user)
    {
        $entity = $user->getUserInfoObject()->getEntityObject();
        $query = $this->getEntityManager()->createQuery('select na, n from Concrete\Core\Entity\Notification\NotificationAlert na join na.notification n where na.naIsArchived = false and na.user = :user order by n.nDate desc');
        $query->setParameter('user', $entity);

        return $query->getResult();
    }

    /**
     * @param int $id
     *
     * @return \Concrete\Core\Entity\Notification\NotificationAlert|null
     */
    public function findOneById($id)
    {
        return $this->findOneBy(
            ['naID' => $id]
        );
    }
}
