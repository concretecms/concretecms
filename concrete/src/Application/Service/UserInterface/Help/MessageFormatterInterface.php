<?php

namespace Concrete\Core\Application\Service\UserInterface\Help;

interface MessageFormatterInterface
{
    /**
     * Format the message for the help panel.
     */
    public function format(Message $message): string;
}
