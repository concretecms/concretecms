<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

/**
 * @since 5.7.4
 */
interface RegistryInterface
{
    public function registerMessageString($identifier, $message);
    public function registerMessages($messages);
    /**
     * @since 5.7.5
     */
    public function setMessage($identifier, MessageInterface $message);
}
