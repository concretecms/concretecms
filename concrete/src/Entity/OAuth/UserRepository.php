<?php

namespace Concrete\Core\Entity\OAuth;

use Concrete\Core\Entity\Express\EntityRepository;
use Concrete\Core\Entity\User\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{

    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $user = new \Concrete\Core\User\User($username, $password);
        if ($user && !$user->isError() && $user->isActive()) {
            return $this->getEntityManager()->getRepository(User::class)->find($user->getUserID());
        }
    }
}
