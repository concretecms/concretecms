<?php

namespace Concrete\Tests\Validator\String;

use Concrete\Core\Entity\Validator\UsedString;
use Concrete\Core\Validator\String\ReuseValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;
use ReflectionMethod;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Entity\User\User as EntityUser;

class ReuseValidatorTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    public function testValidating()
    {
        $repository = M::mock(ObjectRepository::class);
        $repository->shouldReceive('findBy')->with(['subject' => 1337], ['id' => 'desc'], 4)->twice()->andReturn([
            $this->mockUsedString('foobar'),
            $this->mockUsedString('trustno1'),
            $this->mockUsedString('foo'),
            $this->mockUsedString('batteriesaredangerous'),
        ]);

        $entityManager = M::mock(EntityManagerInterface::class);
        $entityManager->shouldReceive('getRepository')->with(UsedString::class)->andReturn($repository);

        $validator = new ReuseValidator($entityManager, 4);

        $this->assertTrue($validator->isValidFor('bar', 1337));
        $this->assertFalse($validator->isValidFor('foo', 1337));
    }

    private function mockUsedString($string, $subject = 1337)
    {
        return M::mock(UsedString::class)->shouldReceive([
            'getUsedString' => password_hash($string, PASSWORD_DEFAULT),
            'getSubject' => $subject
        ])->getMock();
    }

    public function testUserNegotiation()
    {
        $method = new ReflectionMethod(ReuseValidator::class, 'resolveUserID');
        $method->setAccessible(true);

        $entityManager = M::mock(EntityManagerInterface::class);
        $tracker = new ReuseValidator($entityManager, 1);

        $id = 1337;
        $user = M::mock(User::class)->shouldReceive(['getUserID' => $id])->getMock();
        $userInfo = M::mock(UserInfo::class)->shouldReceive(['getUserID' => $id])->getMock();
        $userEntity = M::mock(EntityUser::class)->shouldReceive(['getUserID' => $id])->getMock();

        $this->assertEquals($id, $method->invoke($tracker, 1337));
        $this->assertEquals($id, $method->invoke($tracker, $user));
        $this->assertEquals($id, $method->invoke($tracker, $userInfo));
        $this->assertEquals($id, $method->invoke($tracker, $userEntity));
    }

}
