<?php
namespace Concrete\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * @since 5.7.5.4
 */
interface SessionValidatorInterface
{
    /**
     * Handle invalidating a session
     * This method MUST manage invalidating the session.
     *
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function handleSessionValidation(SymfonySession $session);
}
