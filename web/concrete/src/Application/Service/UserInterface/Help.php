<?php
namespace Concrete\Core\Application\Service\UserInterface;

use Concrete\Core\Application\Service\UserInterface\Help\Formatter;
use Concrete\Core\Application\Service\UserInterface\Help\Message;
use Concrete\Core\Application\Service\UserInterface\Help\MessageInterface;
use Concrete\Core\Application\Service\UserInterface\Help\StandardManager;
use User;
use Core;
use Config;

class Help
{

    public function display()
    {
        if(!Config::get('concrete.accessibility.display_help_system')) {
            return false;
        }

        $args = func_get_args();
        $manager = null;
        $identifier = null;
        if (count($args) == 2) {
            $type = $args[0];
            $manager = Core::make('help/' . $type);
            $identifier = $args[1];
        } else if (count($args) == 1) {
            // Then we just create a message object with the contents of this message.
            $manager = new StandardManager();
            $message = new Message();
            $message->setIdentifier(Core::make('helper/validation/identifier')->getString(12));
            $message->setMessageContent($args[0]);
        }

        if (!isset($message)) {
            $message = $manager->getMessage($identifier);
        }
        if ($message instanceof MessageInterface) {
            $formatter = $manager->getFormatter($message);
            print $formatter->getLauncherHtml($message) . $formatter->getMessageHtml($message);
        }
    }

    public function displayHelpDialogLauncher()
    {
        if(!Config::get('concrete.accessibility.display_help_system')) {
            return false;
        }

        $html =<<<EOT
        <div class="ccm-notification-help-launcher">
            <a href="#" data-help-launch-dialog="main"><i class="fa fa-question-circle"></i></a>
        </div>
EOT;
        return $html;

    }

}