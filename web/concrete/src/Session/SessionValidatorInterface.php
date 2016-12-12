<?php
namespace Concrete\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

interface SessionValidatorInterface
{

    /**
     * Handle invalidating a session
     * This method MUST manage invalidating the session
     *
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @return void
     */
    public function handleSessionValidation(SymfonySession $session);
}
