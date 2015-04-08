<?php
namespace Concrete\Core\Application\Service\UserInterface;

use Concrete\Core\Application\Service\UserInterface\Help\Formatter;
use User;
use Core;

class Help
{

    public function display()
    {
        $args = func_get_args();
        $manager = null;
        $identifier = null;
        if (count($args) == 2) {
            $type = $args[0];
            $manager = Core::make('help/' . $type);
            $identifier = $args[1];
        } else if (count($args) == 1) {
            $type = '';
            $manager = Core::make('help');
            $identifier = $args[0];
        }

        if (!is_object($manager) || !$identifier) {
            return;
        }

        $formatter = new Formatter();
        $message = $manager->getMessage($identifier);
        print $formatter->getLauncherHtml($message) . $formatter->getMessageHtml($message);
    }


}