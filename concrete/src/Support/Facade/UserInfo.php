<?php
namespace Concrete\Core\Support\Facade;

class UserInfo extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\User\UserInfoRepository';
    }

    /**
     * @deprecated
     */
    public static function add($data)
    {
        $cms = static::getFacadeApplication();
        return $cms->make('user/registration')->create($data);
    }

    /**
     * @deprecated
     */
    public static function addSuperUser($uPasswordEncrypted, $uEmail)
    {
        $cms = static::getFacadeApplication();
        return $cms->make('user/registration')->createSuperUser($uPasswordEncrypted, $uEmail);
    }

    /**
     * @deprecated
     */
    public static function register($data)
    {
        $cms = static::getFacadeApplication();
        return $cms->make('user/registration')->createFromPublicRegistration($data);
    }
}
