<?php

namespace Concrete\Core\Api\OAuth;

use Concrete\Core\Entity\User\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Determines whether the user an OAuth token was issued to may still use the API.
 *
 * Tokens outlive the state of the account they were issued to - an access token is good for an hour,
 * an auth code for a day, a refresh token for a month - and the League server never re-checks the user
 * once a token has been issued. This is the single place that decides what "still a valid API user"
 * means, so that every point which turns a token back into API authority agrees on the answer.
 *
 * The checks mirror those \Concrete\Core\User\User::checkLogin() applies to a web session, minus the
 * uLastPasswordChange comparison: that one is made against a value stored in the session at login, and
 * no equivalent issue time is recorded against a token.
 */
class UserStatusValidator
{

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Is the user with the given ID still allowed to authenticate against the API?
     *
     * @param int|string|null $userIdentifier The user ID a token was issued to. Tokens issued through
     *                                        the client credentials grant have no user; callers should
     *                                        not consult this validator for those.
     *
     * @return bool
     */
    public function isValid($userIdentifier)
    {
        if (!$userIdentifier) {
            return false;
        }

        $user = $this->entityManager->find(UserEntity::class, (int) $userIdentifier);

        if (!$user instanceof UserEntity) {
            // The account has been deleted.
            return false;
        }

        if (!$user->isUserActive()) {
            return false;
        }

        if ($user->isUserPasswordReset()) {
            // The account is flagged for a password reset and cannot log in through the web UI, so it
            // should not retain API access either.
            return false;
        }

        return true;
    }
}
