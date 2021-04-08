<?php

namespace Concrete\Core\Entity\User;

use Carbon\Carbon;
use Concrete\Core\User\UserInfo;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;

class LoginAttemptRepository extends EntityRepository
{

    /**
     * Get a list of login attempts prior to a date
     *
     * @param \DateTime $before Must be in UTC
     * @param \Concrete\Core\Entity\User\User|int $user
     * @param bool $count Whether we return an integer count, or an iterator of matches
     *
     * @return \Iterator|\Concrete\Core\Entity\User\LoginAttempt[]|int
     */
    public function before(DateTime $before, $user = null, $count = false)
    {
        // Validate and normalize input
        $before = $this->validateTimezone($before);
        $user = $this->validateUser($user);

        // Build the query builder
        $qb = $this->createQueryBuilder('a');

        if ($count) {
            $qb->select('count(a)');
        } else {
            $qb->select();
        }

        $qb->where('a.utcDate < :before')->setParameter('before', $before);

        // Pivot on user id if needed
        if ($user) {
            $qb->andWhere('a.userId=:user')->setParameter('user', $user);
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (UnexpectedResultException $e) {
                return 0;
            }
        } else {
            // Return an iterator that contains all login attempts before a given date
            return $qb->getQuery()->iterate();
        }
    }

    /**
     * Get a list of login attempts after a given date
     *
     * @param \DateTime $after Must be in UTC
     * @param \Concrete\Core\Entity\User\User|int $user
     * @param bool $count Whether we return an integer count, or an iterator of matches
     *
     * @return \Iterator|\Concrete\Core\Entity\User\LoginAttempt[]|int
     */
    public function after(DateTime $after, $user = null, $count = false)
    {
        // Validate and normalize input
        $after = $this->validateTimezone($after);
        $user = $this->validateUser($user);

        // Build the query builder
        $qb = $this->createQueryBuilder('a');

        if ($count) {
            $qb->select('count(a)');
        } else {
            $qb->select();
        }

        $qb->where('a.utcDate > :after')->setParameter('after', $after->getTimestamp());

        // Pivot on user id if needed
        if ($user) {
            $qb->andWhere('a.userId=:user')->setParameter('user', $user);
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (UnexpectedResultException $e) {
                return 0;
            }
        } else {
            // Return an iterator that contains all login attempts before a given date
            return $qb->getQuery()->iterate();
        }
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return \Carbon\Carbon
     *
     * @throws \InvalidArgumentException
     */
    private function validateTimezone(DateTime $dateTime)
    {
        $dateTime = Carbon::instance($dateTime);

        // Make sure we have the proper timezone. This is to prevent a developer accidentally removing more than they
        // expect to remove
        if ($dateTime->getTimezone()->getName() !== 'UTC') {
            throw new \InvalidArgumentException('Passed datetime is not in UTC timezone.');
        }

        return $dateTime;
    }

    /**
     * Validate a passed user value and resolve the ID
     *
     * @param mixed $user
     * @param bool $requireValue Whether a user value is required
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException
     */
    private function validateUser($user, $requireValue = false)
    {
        // If we're passed something falsy, just return null
        if (!$user && !$requireValue) {
            return null;
        }

        // If we have a known supported type, resolve the id and return it
        if ($user instanceof User || $user instanceof UserInfo) {
            return (int) $user->getUserID();
        }

        // If what we're left with is numeric, return it as an int
        if (is_numeric($user)) {
            return (int) $user;
        }

        throw new \InvalidArgumentException('Invalid user value passed, must be User instance or int');
    }
}
