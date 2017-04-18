<?php
namespace Concrete\Core\Express\Controller;

use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Entry\Notifier\NotifierInterface;
use Concrete\Core\Form\Context\ContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;

interface ControllerInterface extends ContextProviderInterface
{

    function getContextRegistry();
    function getFormProcessor();
    function getEntryManager(Request $request);

    /**
     * @return NotifierInterface
     */
    function getNotifier(NotificationProviderInterface $provider = null);


}
