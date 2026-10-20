<?php

namespace Concrete\Tests\User;

use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\TestHelpers\User\UserTestCase;
use Doctrine\ORM\EntityManagerInterface;

class UserValidationStatusTest extends UserTestCase
{
    public static function provideValidationStatuses(): array
    {
        return [
            [-1, UserEntity::VALIDATION_STATUS_UNKNOWN, true],
            [0, UserEntity::VALIDATION_STATUS_NOT_VALIDATED, false],
            [1, UserEntity::VALIDATION_STATUS_VALIDATED, true],
        ];
    }

    /**
     * @dataProvider provideValidationStatuses
     */
    public function testValidationStatusSurvivesHydration(int $rawValue, int $expectedStatus, bool $expectedIsValidated): void
    {
        $ui = UserInfo::add(['uName' => "validation{$rawValue}", 'uEmail' => "validation{$rawValue}@example.com"]);
        $uID = (int) $ui->getUserID();
        app(EntityManagerInterface::class)->getConnection()->executeStatement('UPDATE Users SET uIsValidated = ? WHERE uID = ?', [$rawValue, $uID]);
        app(EntityManagerInterface::class)->clear();

        $ui = app(UserInfoRepository::class)->getByID($uID);
        $this->assertSame($expectedStatus, $ui->getEntityObject()->getUserValidationStatus());
        $this->assertSame($expectedStatus, $ui->getValidationStatus());
        $this->assertSame($expectedIsValidated, $ui->getEntityObject()->isUserValidated());
        $this->assertSame($expectedIsValidated, $ui->isValidated());
    }

    public function testSetUserIsValidatedAcceptsBooleans(): void
    {
        $entity = new UserEntity();
        $this->assertSame(UserEntity::VALIDATION_STATUS_UNKNOWN, $entity->getUserValidationStatus());
        $entity->setUserIsValidated(true);
        $this->assertSame(UserEntity::VALIDATION_STATUS_VALIDATED, $entity->getUserValidationStatus());
        $entity->setUserIsValidated(false);
        $this->assertSame(UserEntity::VALIDATION_STATUS_NOT_VALIDATED, $entity->getUserValidationStatus());
        $entity->setUserIsValidated(UserEntity::VALIDATION_STATUS_UNKNOWN);
        $this->assertSame(UserEntity::VALIDATION_STATUS_UNKNOWN, $entity->getUserValidationStatus());
    }
}
