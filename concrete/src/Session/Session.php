<?php
namespace Concrete\Core\Session;

use Concrete\Core\Application\Application;
use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * Class Session.
 *
 * \@package Concrete\Core\Session
 *
 * @deprecated
 */
class Session
{

    /** @var Application */
    protected static $app;

    /**
     * DO NOT USE THIS METHOD
     * Instead override the application bindings.
     * This method only exists to enable legacy static methods on the real application instance
     * @deprecated Create the session using $app->make('session');
     */
    public static function setApplicationObject(Application $app)
    {
        static::$app = $app;
    }

    /**
     * @deprecated Create the session using $app->make('session');
     */
    public static function start()
    {
        /** @var FactoryInterface $factory */
        return self::$app->make('session');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     *
     * @deprecated Use \Concrete\Core\Session\SessionValidator
     */
    public static function testSessionFixation(SymfonySession $session)
    {
        $validator = self::$app->make('Concrete\Core\Session\SessionValidatorInterface');
        $validator->handleSessionValidation($session);
    }
}
