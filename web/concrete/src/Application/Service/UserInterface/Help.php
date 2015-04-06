<?php
namespace Concrete\Core\Application\Service\UserInterface;

use Concrete\Core\Application\Service\UserInterface\Help\Message;
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

        $message = $manager->getMessage($identifier);
        $content = $message->getContent();

        $ok = t('Ok');
        $hideAll = t('Hide All');

        $html =<<<EOT
        <div class="ccm-notification-help-launcher">
            <a href="#" data-help-notification-toggle="{$identifier}"><i class="fa fa-question-circle"></i></a>
        </div>

        <div class="ccm-notification-help ccm-notification" data-help-notification="{$identifier}">
            <i class="ccm-notification-icon fa fa-info-circle"></i>
            <div class="ccm-notification-inner dialog-help">{$content}</div>
            <div class="ccm-notification-actions">
                <a href="#" data-help-notification-identifier="{$identifier}" class="ccm-notification-actions-dismiss-single" data-help-notification-type="{$type}" data-dismiss="help-single">{$ok}</a><a href="#" data-help-notification-identifier="{$identifier}" data-help-notification-type="{$type}" data-dismiss="help-all">{$hideAll}</a>
            </div>
        </div>
EOT;

        print $html;

    }


}