<?php

namespace Concrete\Tests\User;

use Concrete\Core\User\Logger;
use Concrete\Core\User\User;
use Mockery as M;
use Psr\Log\LoggerInterface;

class UserLoggingTest extends \PHPUnit_Framework_TestCase
{

    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected function getUser()
    {
        $user = M::mock(User::class);
        $user->shouldReceive('getUserName')->andReturn('andrew');
        $user->shouldReceive('getUserID')->andReturn(33);
        return $user;
    }

    protected function getApplier()
    {
        $applier = M::mock(User::class);
        $applier->shouldReceive('getUserName')->andReturn('admin');
        $applier->shouldReceive('getUserID')->andReturn(1);
        $applier->shouldReceive('isRegistered')->andReturn(true);
        return $applier;
    }

    protected function testLogger($loggerArgs, $operation, $operationArgs)
    {
        $loggerInterface = M::mock(LoggerInterface::class);
        $loggerInterface->shouldReceive('info')->once()->withArgs($loggerArgs);
        $logger = new Logger();
        $logger->setLogger($loggerInterface);
        call_user_func_array([$logger, $operation], $operationArgs);
    }

    public function testAddUserLoggingEmpty()
    {
        $this->testLogger([
            'User andrew (ID 33) was added by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'add_user'
            ]
        ], 'logAdd', [$this->getUser()]);
    }

    public function testAddUserLoggingApplier()
    {
        $this->testLogger([
            'User andrew (ID 33) was added by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'add_user'
            ]
        ], 'logAdd', [$this->getUser(), $this->getApplier()]);
    }

    public function testChangePasswordLoggingEmpty()
    {
        $this->testLogger([
            'Password for user andrew (ID 33) was changed by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'change_password'
            ]
        ], 'logChangePassword', [$this->getUser()]);
    }

    public function testChangePasswordLoggingApplier()
    {
        $this->testLogger([
            'Password for user andrew (ID 33) was changed by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'change_password'
            ]
        ], 'logChangePassword', [$this->getUser(), $this->getApplier()]);
    }

    public function testResetPasswordEmpty()
    {
        $this->testLogger([
            'Password for user andrew (ID 33) was reset by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'reset_password'
            ]
        ], 'logResetPassword', [$this->getUser()]);
    }

    public function testResetPasswordApplier()
    {
        $this->testLogger([
            'Password for user andrew (ID 33) was reset by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'reset_password'
            ]
        ], 'logResetPassword', [$this->getUser(), $this->getApplier()]);
    }

    public function testUpdateUserEmpty()
    {
        $this->testLogger([
            'User andrew (ID 33) was updated by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'update_user'
            ]
        ], 'logUpdateUser', [$this->getUser()]);
    }

    public function testUpdateUserApplier()
    {
        $this->testLogger([
            'User andrew (ID 33) was updated by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'update_user'
            ]
        ], 'logUpdateUser', [$this->getUser(), $this->getApplier()]);
    }

    public function testActivateUserEmpty()
    {
        $this->testLogger([
            'User andrew (ID 33) was activated by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'activate_user'
            ]
        ], 'logActivateUser', [$this->getUser()]);
    }

    public function testActivateUserApplier()
    {
        $this->testLogger([
            'User andrew (ID 33) was activated by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'activate_user'
            ]
        ], 'logActivateUser', [$this->getUser(), $this->getApplier()]);
    }

    public function testDeactivateUserEmpty()
    {
        $this->testLogger([
            'User andrew (ID 33) was deactivated by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'deactivate_user'
            ]
        ], 'logDeactivateUser', [$this->getUser()]);
    }

    public function testDeactivateUserApplier()
    {
        $this->testLogger([
            'User andrew (ID 33) was deactivated by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'deactivate_user'
            ]
        ], 'logDeactivateUser', [$this->getUser(), $this->getApplier()]);
    }









}
