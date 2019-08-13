<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

/**
 * @since 5.7.4
 */
interface ManagerInterface
{
    /**
     * @param string $identifier
     *
     * @return Message
     */
    public function getMessage($identifier);

    /**
     * @return FormatterInterface
     */
    public function getFormatter(Message $message);
}
