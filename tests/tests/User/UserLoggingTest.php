<?php

namespace Concrete\Tests\User;

use Concrete\Core\User\Event\UserInfoWithPassword;
use Concrete\Core\User\Logger;
use Concrete\Core\User\LogSubscriber;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Mockery as M;
use Psr\Log\LoggerInterface;

class UserLoggingTest extends \PHPUnit_Framework_TestCase
{

    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function getUserInfoWithPasswordEvent()
    {
        $user = M::mock(User::class);
        $user->shouldReceive('getUserName')->andReturn('andrew');
        $user->shouldReceive('getUserID')->andReturn(33);
        $userinfo = M::mock(UserInfo::class);
        $userinfo->shouldReceive('getUserObject')->andReturn($user);
        $event = M::mock(UserInfoWithPassword::class);
        $event->shouldReceive('getUserInfoObject')->andReturn($userinfo);
        return $event;
    }

    public function getUserInfoEvent()
    {
        $user = M::mock(User::class);
        $user->shouldReceive('getUserName')->andReturn('andrew');
        $user->shouldReceive('getUserID')->andReturn(33);
        $userinfo = M::mock(UserInfo::class);
        $userinfo->shouldReceive('getUserObject')->andReturn($user);
        $event = M::mock(\Concrete\Core\User\Event\UserInfo::class);
        $event->shouldReceive('getUserInfoObject')->andReturn($userinfo);
        return $event;
    }

    protected function getApplier()
    {
        $applier = M::mock(User::class);
        $applier->shouldReceive('getUserName')->andReturn('admin');
        $applier->shouldReceive('getUserID')->andReturn(1);
        $applier->shouldReceive('isRegistered')->andReturn(true);
        return $applier;
    }

    protected function doTestLogger($loggerArgs, $operation, $operationArgs)
    {
        $loggerInterface = M::mock(LoggerInterface::class);
        $loggerInterface->shouldReceive('info')->once()->withArgs($loggerArgs);
        $logger = new LogSubscriber();
        $logger->setLogger($loggerInterface);
        call_user_func_array([$logger, $operation], $operationArgs);
    }

    public function testAddUserLoggingEmpty()
    {
        $event = $this->getUserInfoWithPasswordEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'User andrew (ID 33) was added by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'add_user'
            ]
        ], 'onUserAdd', [$event]);
    }

    public function testAddUserLoggingApplier()
    {
        $event = $this->getUserInfoWithPasswordEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'User andrew (ID 33) was added by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'add_user'
            ]
        ], 'onUserAdd', [$event]);
    }

    public function testChangePasswordLoggingEmpty()
    {
        $event = $this->getUserInfoWithPasswordEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'Password for user andrew (ID 33) was changed by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'change_password'
            ]
        ], 'onUserChangePassword', [$event]);
    }

    public function testChangePasswordLoggingApplier()
    {
        $event = $this->getUserInfoWithPasswordEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'Password for user andrew (ID 33) was changed by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'change_password'
            ]
        ], 'onUserChangePassword', [$event]);
    }

    public function testResetPasswordEmpty()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'Password for user andrew (ID 33) was reset by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'reset_password'
            ]
        ], 'onUserResetPassword', [$event]);
    }

    public function testResetPasswordApplier()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'Password for user andrew (ID 33) was reset by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'reset_password'
            ]
        ], 'onUserResetPassword', [$event]);
    }

    public function testUpdateUserEmpty()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'User andrew (ID 33) was updated by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'update_user'
            ]
        ], 'onUserUpdate', [$event]);
    }

    public function testUpdateUserApplier()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'User andrew (ID 33) was updated by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'update_user'
            ]
        ], 'onUserUpdate', [$event]);
    }

    public function testActivateUserEmpty()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'User andrew (ID 33) was activated by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'activate_user'
            ]
        ], 'onUserActivate', [$event]);
    }

    public function testActivateUserApplier()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'User andrew (ID 33) was activated by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'activate_user'
            ]
        ], 'onUserActivate', [$event]);
    }

    public function testDeactivateUserEmpty()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'User andrew (ID 33) was deactivated by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'deactivate_user'
            ]
        ], 'onUserDeactivate', [$event]);
    }

    public function testDeactivateUserApplier()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'User andrew (ID 33) was deactivated by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'deactivate_user'
            ]
        ], 'onUserDeactivate', [$event]);
    }

    public function testDeleteUserEmpty()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn(null);
        $this->doTestLogger([
            'User andrew (ID 33) was deleted by code or an automated process.',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'operation' => 'delete_user'
            ]
        ], 'onUserDeleted', [$event]);
    }

    public function testDeleteUserApplier()
    {
        $event = $this->getUserInfoEvent();
        $event->shouldReceive('getApplier')->andReturn($this->getApplier());
        $this->doTestLogger([
            'User andrew (ID 33) was deleted by admin (ID 1).',
            [
                'user_id' => 33,
                'user_name' => 'andrew',
                'applier_id' => 1,
                'applier_name' => 'admin',
                'operation' => 'delete_user'
            ]
        ], 'onUserDeleted', [$event]);
    }










}
