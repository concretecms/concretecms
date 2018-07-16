<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use Concrete\Core\Validator\ValidatorForSubjectInterface;
use InvalidArgumentException;

/**
 * Validate the length of a string.
 */
class UniqueUserNameValidator extends AbstractTranslatableValidator implements ValidatorForSubjectInterface
{
    /**
     * The username is already in use.
     *
     * @var int
     */
    const E_USERNAME_IN_USE = 1;

    /**
     * The username is already in use by another user.
     *
     * @var int
     */
    const E_USERNAME_USED_BY_ANOTHER_USER = 2;

    /**
     * The connection to the database.
     *
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * UniqueUserNameValidator constructor.
     *
     * @param \Concrete\Core\Database\Connection\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->setRequirementString(
            self::E_USERNAME_IN_USE,
            function (UniqueUserNameValidator $validator, $code) {
                return t('A username must be unique.');
            }
        );
        $this->setRequirementString(
            self::E_USERNAME_USED_BY_ANOTHER_USER,
            function (UniqueUserNameValidator $validator, $code) {
                return t('A username must be unique.');
            }
        );
        $this->setErrorString(
            self::E_USERNAME_IN_USE,
            function (UniqueUserNameValidator $validator, $code, $mixed) {
                return t('The username "%s" is already taken.', $mixed);
            }
        );
        $this->setErrorString(
            self::E_USERNAME_USED_BY_ANOTHER_USER,
            function (UniqueUserNameValidator $validator, $code, $mixed) {
                return t('The username "%s" is already used by another user.', $mixed);
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        return $this->isValidFor($mixed, null, $error);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorForSubjectInterface::isValidFor()
     *
     * @param int|\Concrete\Core\User\User|\Concrete\Core\Entity\User\User|\Concrete\Core\User\UserInfo|null $subject
     */
    public function isValidFor($mixed, $subject = null, ArrayAccess $error = null)
    {
        if (!is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
        }
        $uID = 0;
        if ($subject) {
            if (is_object($subject)) {
                if (!method_exists($subject, 'getUserID')) {
                    throw new InvalidArgumentException(t('Invalid subject type supplied to validator.'));
                }
                $uID = (int) $subject->getUserID();
            } else {
                $uID = (int) $subject;
            }
        } else {
            $uID = 0;
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('u.uID')
            ->from('Users', 'u')
            ->where($qb->expr()->eq('u.uName', $qb->createNamedParameter($mixed)))
        ;
        if ($uID !== 0) {
            $qb->andWhere($qb->expr()->neq('u.uID', $qb->createNamedParameter($uID)));
        }

        if ($qb->execute()->fetchColumn() !== false) {
            if ($error) {
                $message = $this->getErrorString($uID === 0 ? self::E_USERNAME_IN_USE : self::E_USERNAME_USED_BY_ANOTHER_USER, $mixed);
                if ($message) {
                    $error[] = $message;
                }
            }

            return false;
        }

        return true;
    }
}
