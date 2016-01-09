<?php
namespace Concrete\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Interface FactoryInterface
 * An object that can create symfony sessions
 *
 * @package Concrete\Core\Session
 */
interface SessionFactoryInterface
{

    /**
     * Create a new symfony session object
     * This method MUST NOT start the session
     *
     * @return Session
     */
    public function createSession();

}
