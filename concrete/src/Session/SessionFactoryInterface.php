<?php
namespace Concrete\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * An object that can create symfony sessions.
 */
interface SessionFactoryInterface
{
    /**
     * Create a new symfony session object
     * This method MUST NOT start the session.
     *
     * @return SymfonySession
     */
    public function createSession();
}
