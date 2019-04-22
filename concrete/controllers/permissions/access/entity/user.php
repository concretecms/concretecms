<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validation\CSRF\Token;

class User extends AccessEntity
{

    /**
     * @var UserInfoRepository
     */
    protected $userInfoRepository;

    public function __construct(UserInfoRepository $userInfoRepository, Token $token)
    {
        $this->userInfoRepository = $userInfoRepository;
        parent::__construct($token);
    }

    public function deliverEntity()
    {
        $user = $this->userInfoRepository->getByID($this->request->query->get('uID'));
        if ($user) {
            return UserEntity::getOrCreate($user);
        }
    }
}
