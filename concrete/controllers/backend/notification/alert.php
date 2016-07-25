<?php
namespace Concrete\Controller\Backend\Notification;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class Alert extends AbstractController
{

    protected $entityManager;
    protected $token;

    public function __construct(EntityManager $entityManager, Token $token)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->token = $token;
    }

    public function archive()
    {
        $id = intval($this->request->request->get('naID'));
        if ($this->token->validate() && $id > 0) {
            $u = new \User();
            if ($u->isRegistered()) {
                $r = $this->entityManager->getRepository('Concrete\Core\Entity\Notification\NotificationAlert');
                $alert = $r->findOneById($id);
                if (is_object($alert) &&
                    is_object($alert->getUser()) &&
                    $alert->getUser()->getUserID() == $u->getUserID()) {

                    $alert->setNotificationIsArchived(true);
                    $this->entityManager->persist($alert);
                    $this->entityManager->flush();

                }
            }
        }
        $this->app->shutdown();
    }
}
