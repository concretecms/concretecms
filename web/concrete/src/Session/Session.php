<?php
namespace Concrete\Core\Session;

use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * Class Session.
 *
 * @package Concrete\Core\Session
 *
 * @deprecated
 */
class Session
{
    /**
     * Class Session.
     *
     * @package Concrete\Core\Session
     *
     * @deprecated Create the session using $app->make('session');
     */
    public static function start()
    {
        /* @var FactoryInterface $factory */
        return Application::make('session');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     *
     * @deprecated Use \Concrete\Core\Session\SessionValidator
     */
    public static function testSessionFixation(SymfonySession $session)
    {
        $validator = Application::make('Concrete\Core\Session\SessionValidatorInterface');
        $validator->handleSessionValidation($session);
    }
}
