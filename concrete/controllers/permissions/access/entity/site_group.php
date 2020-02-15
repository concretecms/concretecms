<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Entity\Site\Group\Group as GroupEntity;
use Concrete\Core\Permission\Access\Entity\SiteGroupEntity;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManagerInterface;

class SiteGroup extends AccessEntity
{

    protected $entityManager;
    protected $application;

    public function __construct(Token $token, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($token);
    }

    public function deliverEntity()
    {
        $group = $this->entityManager->find(GroupEntity::class,
            $this->request->request->get('siteGID')
        );
        if ($group) {
            return SiteGroupEntity::getOrCreate($group);
        }
    }

}
