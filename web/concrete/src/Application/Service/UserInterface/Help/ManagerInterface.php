<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

interface ManagerInterface
{

    /**
     * @param string $identifier
     * @return Message
     */
    public function getMessage($identifier);

}
