<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

interface RegistryInterface
{

    public function registerMessageString($identifier, $message);
    public function registerMessages($messages);
}
