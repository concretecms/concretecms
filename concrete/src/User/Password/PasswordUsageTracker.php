<?php

namespace Concrete\Core\User\Password;

use Carbon\Carbon;
use Concrete\Core\Entity\User\User as EntityUser;
use Concrete\Core\Entity\Validator\UsedString;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\EntityManagerInterface;

class PasswordUsageTracker
{

    /**
     * The entity manager we're saving usages against
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * The number of used strings we will track per subject
     *
     * @var int
     */
    protected $maxReuse;

    public function __construct(EntityManagerInterface $entityManager, $maxReuse)
    {
        $this->entityManager = $entityManager;
        $this->maxReuse = $maxReuse;
    }

    /**
     * Track a string being used
     *
     * @param string $string The password that was used
     * @param int|\Concrete\Core\User\User|\Concrete\Core\User\UserInfo|\Concrete\Core\Entity\User\User $subject The subject that used the password
     *
     * @return bool
     */
    public function trackUse($string, $subject)
    {
        $id = $this->resolveUserID($subject);

        // If the subject is invalid return false
        if (!$id) {
            return false;
        }

        if (!is_string($string)) {
            throw new \InvalidArgumentException(t('Invalid mixed value provided. Must be a string.'));
        }

        if ($this->maxReuse) {
            // Store the use in the database
            $this->entityManager->transactional(function (EntityManagerInterface $em) use ($id, $string) {
                $reuse = new UsedString();
                $reuse->setDate(Carbon::now());
                $reuse->setSubject($id);
                $reuse->setUsedString(password_hash($string, PASSWORD_DEFAULT));

                $em->persist($reuse);
            });
        }

        // Prune extra tracked uses
        $this->pruneUses($id);

        return true;
    }

    /**
     * Prune uses for a specific subject
     *
     * @param int $subject
     */
    private function pruneUses($subject)
    {
        $repository = $this->entityManager->getRepository(UsedString::class);
        $usedStrings = $repository->findBy(['subject' => $subject], ['id' => 'desc']);

        // IF there are extra used strings, prune the extras
        if (count($usedStrings) > $this->maxReuse) {
            array_map([$this->entityManager, 'remove'], array_slice($usedStrings, $this->maxReuse));
            $this->entityManager->flush();
        }
    }

    /**
     * @param $subject
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    private function resolveUserID($subject)
    {
        // Handle `null`, `0`, and any other falsy subject
        if (!$subject) {
            return 0;
        }

        // If an integer is just passed in
        if (is_numeric($subject)) {
            return (int)$subject;
        }

        // If we get an actual user instance
        if ($subject instanceof User || $subject instanceof UserInfo || $subject instanceof EntityUser) {
            return $subject->getUserID();
        }

        // Non-falsy subject that is unsupported
        throw new \InvalidArgumentException(t('Unsupported subject provided. Subject must be a User, UserInfo, or User Entity object.'));
    }

}
