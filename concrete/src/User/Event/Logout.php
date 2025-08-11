<?php

namespace Concrete\Core\User\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

class Logout extends GenericEvent
{
    protected const RESPONSE_ARGUMENT_KEY = 'response';

    public function __construct(?int $userID)
    {
        parent::__construct($userID, [static::RESPONSE_ARGUMENT_KEY => null]);
    }

    /**
     * Get the response to be sent to clients after the logout process (if available).
     */
    public function getResponse(): ?Response
    {
        $response = $this->getArgument(static::RESPONSE_ARGUMENT_KEY);

        return $response instanceof Response ? $response : null;
    }

    /**
     * Set the response to be sent to clients after the logout process.
     *
     * @return $this
     */
    public function setResponse(?Response $response): self
    {
        return $this->setArgument(static::RESPONSE_ARGUMENT_KEY, $response);
    }
}
