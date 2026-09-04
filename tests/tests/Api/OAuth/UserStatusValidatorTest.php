<?php

namespace Concrete\Tests\Api\OAuth;

use Concrete\Core\Api\OAuth\UserStatusValidator;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\TestHelpers\User\UserTestCase;
use Core;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Covers the predicate that every OAuth entry point consults to decide whether the user a token was
 * issued to may still use the API.
 *
 * @covers \Concrete\Core\Api\OAuth\UserStatusValidator
 */
class UserStatusValidatorTest extends UserTestCase
{

    /**
     * @var \Concrete\Core\Api\OAuth\UserStatusValidator
     */
    protected $validator;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = Core::make(EntityManagerInterface::class);
        $this->validator = Core::make(UserStatusValidator::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->truncateTables();
    }

    /**
     * Persist a user in a given state and return its ID.
     *
     * @param bool $isActive
     * @param bool $isPasswordReset
     *
     * @return int
     */
    protected function createUserEntity($isActive = true, $isPasswordReset = false)
    {
        $user = new UserEntity();
        $user->setUserName('u' . uniqid());
        $user->setUserEmail(uniqid() . '@example.com');
        $user->setUserPassword('irrelevant-for-these-tests');
        $user->setUserIsActive($isActive);
        $user->setUserIsPasswordReset($isPasswordReset);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $uID = $user->getUserID();

        // Read back through a clean identity map, the way a fresh API request would.
        $this->entityManager->clear();

        return $uID;
    }

    public function testActiveUserIsValid()
    {
        $uID = $this->createUserEntity(true, false);

        $this->assertTrue(
            $this->validator->isValid($uID),
            'An active user with no password reset flag should retain API access.'
        );
    }

    /**
     * Finding #1: a deactivated user must lose API access immediately, not when their token expires.
     */
    public function testDeactivatedUserIsRejected()
    {
        $uID = $this->createUserEntity(true, false);
        $this->assertTrue($this->validator->isValid($uID), 'Precondition: user starts out valid.');

        // Deactivate exactly as UserInfo::deactivate() does - a straight write to uIsActive, with no
        // token revocation of any kind.
        $this->entityManager->getConnection()->executeQuery(
            'update Users set uIsActive = 0 where uID = ?',
            [$uID]
        );
        $this->entityManager->clear();

        $this->assertFalse(
            $this->validator->isValid($uID),
            'A deactivated user must not be able to authenticate against the API.'
        );
    }

    /**
     * Finding #3: uIsActive is only part of what checkLogin() gates on. A user locked out of the web UI
     * by uIsPasswordReset must not keep API access.
     */
    public function testPasswordResetFlaggedUserIsRejected()
    {
        $uID = $this->createUserEntity(true, false);
        $this->assertTrue($this->validator->isValid($uID), 'Precondition: user starts out valid.');

        // Flag for reset exactly as UserInfo::markAsPasswordReset() does.
        $this->entityManager->getConnection()->executeQuery(
            'UPDATE Users SET uIsPasswordReset = 1 WHERE uID = ?',
            [$uID]
        );
        $this->entityManager->clear();

        $this->assertFalse(
            $this->validator->isValid($uID),
            'A user flagged for a password reset cannot log in through the web UI and must not keep API access.'
        );
    }

    public function testDeletedUserIsRejected()
    {
        $uID = $this->createUserEntity(true, false);
        $this->assertTrue($this->validator->isValid($uID), 'Precondition: user starts out valid.');

        $this->entityManager->getConnection()->executeQuery('delete from Users where uID = ?', [$uID]);
        $this->entityManager->clear();

        $this->assertFalse(
            $this->validator->isValid($uID),
            'A token issued to a since-deleted user must not authenticate.'
        );
    }

    /**
     * Tokens issued through the client credentials grant carry no user. Callers guard against this
     * before consulting the validator, but the validator itself must never treat "no user" as valid.
     *
     * @dataProvider provideEmptyIdentifiers
     */
    public function testEmptyIdentifierIsRejected($identifier)
    {
        $this->assertFalse(
            $this->validator->isValid($identifier),
            'An empty user identifier must never be treated as a valid user.'
        );
    }

    public static function provideEmptyIdentifiers()
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'zero' => [0],
            'string zero' => ['0'],
        ];
    }
}
