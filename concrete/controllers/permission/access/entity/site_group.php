<?php
namespace Concrete\Core\Controller\Permission\Access\Entity;

use Concrete\Core\Application\Application;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Site\Group\Group;
use Concrete\Core\Permission\Access\Entity\SiteGroupEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SiteGroup extends Controller
{

    protected $entityManager;
    protected $application;

    public function __construct(Application $application, EntityManagerInterface $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    public function process()
    {
        $tp = new \Permissions();
        if ($tp->canAccessTaskPermissions()) {
            $obj = new \stdClass;
            if ($this->application->make('token')->validate('process')) {
                $group = $this->entityManager->find(Group::class,
                    $this->request->request->get('siteGID'));
                if (is_object($group)) {
                    $pae = SiteGroupEntity::getOrCreate($group);
                    $obj->peID = $pae->getAccessEntityID();
                    $obj->label = $pae->getAccessEntityLabel();
                }
            }
            return new JsonResponse($obj);
        }
    }

}
