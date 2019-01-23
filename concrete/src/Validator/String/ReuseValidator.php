<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Entity\User\User as EntityUser;
use Concrete\Core\Entity\Validator\UsedString;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use Concrete\Core\Validator\ValidatorForSubjectInterface;
use Concrete\Core\Validator\ValidatorForSubjectTrait;
use Doctrine\ORM\EntityManagerInterface;

class ReuseValidator extends AbstractTranslatableValidator implements ValidatorForSubjectInterface
{

    use ValidatorForSubjectTrait;

    const E_PASSWORD_RECENTLY_USED = 1;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var int
     */
    private $maxReuse;

    public function __construct(EntityManagerInterface $entityManager, $maxReuse = 5)
    {
        $this->entityManager = $entityManager;
        $this->maxReuse = $maxReuse;

        $this->setErrorString(self::E_PASSWORD_RECENTLY_USED, t("You've recently used this password, please try a unique password"));
    }

    /**
     * Is this mixed value valid for the specified (optional) subject?
     *
     * @param mixed $mixed Can be any value, should be a string to be valid
     * @param mixed|int $subject The user ID the mixed value needs to be valid for
     * @param \ArrayAccess|null $error
     *
     * @throws \InvalidArgumentException throws a InvalidArgumentException when $mixed or $subject are not valid
     *
     * @return bool
     */
    public function isValidFor($mixed, $subject = null, ArrayAccess $error = null)
    {
        $id = $this->resolveUserID($subject);

        // If no subject is provided then we have no data to check against
        if (!$id) {
            return true;
        }

        // Validate the provided mixed type
        if (!is_string($mixed)) {
            throw new \InvalidArgumentException(t('Invalid mixed value provided. Must be a string.'));
        }

        // If the password hasn't been used it's valid
        if (!$this->hasBeenUsed($mixed, $id)) {
            return true;
        }

        // If the password has recently been used, it's invalid
        if ($error) {
            $error->add($this->getErrorString(self::E_PASSWORD_RECENTLY_USED, $mixed));
        }

        return false;
    }

    /**
     * Check whether a string has been used against an id
     *
     * @param string $string
     * @param int$id
     *
     * @return bool
     */
    private function hasBeenUsed($string, $id)
    {
        $repository = $this->entityManager->getRepository(UsedString::class);
        $allUses = $repository->findBy(['subject' => $id], ['id' => 'desc'], $this->maxReuse);

        foreach ($allUses as $use) {
            if ($this->matches($string, $use)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify whether an obfuscated password matches a tracked used password
     *
     * @param string $string
     * @param UsedString $usedString
     *
     * @return bool
     */
    private function matches($string, UsedString $usedString)
    {
        return password_verify($string, $usedString->getUsedString());
    }

    /**
     * Protected accessor method for subclasses
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Protected accessor method for subclasses
     *
     * @return int
     */
    protected function getMaxReuseCount()
    {
        return $this->maxReuse;
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
            return (int) $subject;
        }

        // If we get an actual user instance
        if ($subject instanceof User || $subject instanceof UserInfo || $subject instanceof EntityUser) {
            return $subject->getUserID();
        }

        // Non-falsy subject that is unsupported
        throw new \InvalidArgumentException(t('Unsupported subject provided. Subject must be a User, UserInfo, or User Entity object.'));
    }
}
