<?php

namespace Concrete\Core\Authentication\Type\OAuth;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Doctrine\DBAL\Driver\Statement;
use Throwable;

class BindingService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var DatabaseManager */
    protected $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Clear bindings for BOTH the given binding and for the given user id in a given namespace.
     *
     * @param int $userId The user to clear bindings for
     * @param int|string $binding The binding to clear
     * @param string $namespace The namespace to clear bindings and user ids in
     * @param bool $matchBoth Match the userID and binding instead of one or the other
     *
     * @return int
     */
    public function clearBinding($userId, $binding, $namespace, $matchBoth = false)
    {
        $db = $this->getConnection();

        // Log out details about what's being deleted
        if ($this->logger) {
            $existingUserBound = $this->getBoundUserId($binding, $namespace);
            $existingBindingBound = $this->getUserBinding($userId, $namespace);

            // If we're matching both exactly, notify when an exact match will be deleted
            if ($matchBoth && $existingUserBound === (int) $userId && $existingBindingBound === (string) $binding) {
                $this->logger->warning('Deleting user binding: User #{user} was bound to "{binding}" in "{namespace}".', [
                    'user' => $existingUserBound,
                    'binding' => $binding,
                    'namespace' => $namespace,
                    'matchBoth' => $matchBoth,
                ]);
            }

            // Notify the log of the deletion if it doesn't match what we're inserting
            if ($existingUserBound && $existingUserBound !== $userId) {
                $this->logger->warning('Deleting user binding: User #{user} was bound to "{binding}" in "{namespace}".', [
                    'user' => $existingUserBound,
                    'binding' => $binding,
                    'namespace' => $namespace,
                    'matchBoth' => $matchBoth,
                ]);
            }

            // Notify the log of the deletion if it doesn't match what we're inserting
            if ($existingBindingBound && $existingBindingBound !== (string) $binding) {
                $this->logger->warning('Deleting user binding: User #{user} was bound to "{binding}" in "{namespace}".', [
                    'user' => $userId,
                    'binding' => $existingBindingBound,
                    'namespace' => $namespace,
                    'matchBoth' => $matchBoth,
                ]);
            }
        }

        // Clear all bindings that match the existing user id OR binding
        $total = 0;
        try {
            $db->transactional(static function (Connection $db) use ($userId, $binding, $namespace, $matchBoth, &$total) {
                $qb = $db->createQueryBuilder();
                $ex = $qb->expr();

                $userMatch = $ex->eq('user_id', ':id');
                $bindingMatch = $ex->eq('binding', ':binding');
                $matches = $matchBoth ? $ex->andX($userMatch, $bindingMatch) : $ex->orX($userMatch, $bindingMatch);

                $qb->delete('OauthUserMap');
                $qb->where($ex->eq('namespace', ':namespace'))->andWhere($matches);
                $qb->setParameters([
                    'namespace' => (string) $namespace,
                    'id' => (int) $userId,
                    'binding' => $binding,
                ]);

                $total = $qb->execute();
                if ($total instanceof Statement) {
                    $total = $total->fetchColumn();
                }
            });
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to delete binding.');
        }

        return $total;
    }

    /**
     * Get the bound user id.
     *
     * @param int|string $binding
     * @param string $namespace
     *
     * @return int|null Returns null when no user id is bound
     */
    public function getBoundUserId($binding, $namespace)
    {
        /** @var Connection $db */
        $db = $this->getConnection();
        $qb = $db->createQueryBuilder();
        $ex = $qb->expr();
        $result = $qb->select('user_id')->from('OauthUserMap')
            ->where($ex->eq('namespace', ':namespace'))
            ->andWhere($ex->eq('binding', ':binding'))
            ->setParameters([
                'namespace' => $namespace,
                'binding' => $binding,
            ])
            ->execute()->fetchColumn();

        if ($result === false) {
            return null;
        }

        return (int) $result;
    }

    /**
     * Get the bound binding.
     *
     * @param int $userId The user ID whose binding is being searched
     * @param string $namespace The namespace to find a binding in
     *
     * @return string|null Returns null when no binding is bound, this method will return a string even if an int was bound
     */
    public function getUserBinding($userId, $namespace)
    {
        /** @var Connection $db */
        $db = $this->getConnection();

        $qb = $db->createQueryBuilder();
        $ex = $qb->expr();
        $result = $qb->select('binding')->from('OauthUserMap')
            ->where($ex->eq('namespace', ':namespace'))
            ->andWhere($ex->eq('user_id', ':id'))
            ->setParameters([
                'namespace' => $namespace,
                'id' => $userId,
            ])
            ->execute()->fetchColumn();

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * Bind a user against a remote binding in a namespace
     * EX: `$bindings->bindUserId($id, $facebookUserId, 'facebook');`.
     *
     * @param int $id The user ID
     * @param int|string $binding The binding to associate the user with
     * @param string $namespace The namespace for this binding
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function bindUserId($id, $binding, $namespace)
    {
        $id = (int) $id;

        if (!$id) {
            throw new \InvalidArgumentException('Invalid user id provided');
        }

        $this->clearBinding($id, $binding, $namespace);
        $result = $this->getConnection()->insert('OauthUserMap', [
            'user_id' => $id,
            'binding' => $binding,
            'namespace' => $namespace,
        ]);

        if ($result !== 1) {
            throw new \RuntimeException('Failed to bind user.');
        }

        if ($this->logger) {
            $this->logger->warning('Bound user: User #{user} is now bound to "{binding}" in "{namespace}".', [
                'user' => $id,
                'binding' => $binding,
                'namespace' => $namespace,
            ]);
        }

        return true;
    }

    /**
     * Bind a user object to a given binding.
     *
     * @param User $user The user to bind to the binding value
     * @param int|string $binding The value to bind this user to
     * @param string $namespace The namespace for this binding
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function bindUser(User $user, $binding, $namespace)
    {
        return $this->bindUserId((int) $user->getUserID(), $binding, $namespace);
    }

    /**
     * Bind a user info object to a given binding.
     *
     * @param UserInfo $user The user to bind to the binding value
     * @param int|string $binding The value to bind this user to
     * @param string $namespace The namespace for this binding
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function bindUserInfo(UserInfo $user, $binding, $namespace)
    {
        return $this->bindUserId((int) $user->getUserID(), $binding, $namespace);
    }

    /**
     * Bind a user entity object to a given binding.
     *
     * @param UserEntity $user The user to bind to the binding value
     * @param int|string $binding The value to bind this user to
     * @param string $namespace The namespace for this binding
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function bindUserEntity(UserEntity $user, $binding, $namespace)
    {
        return $this->bindUserId((int) $user->getUserID(), $binding, $namespace);
    }

    /**
     * Get the logger channel expected by this LoggerAwareTrait implementation
     * The user is expected to declare this method and return a valid channel name.
     *
     * @return string One of \Concrete\Core\Logging\Channels::CHANNEL_*
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_AUTHENTICATION;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->databaseManager->connection();
    }
}
