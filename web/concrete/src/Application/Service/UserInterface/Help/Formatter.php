<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class Formatter implements FormatterInterface
{
    public function getLauncherHtml(Message $message)
    {
        $identifier = $message->getIdentifier();
        $html =<<<EOT
        <div class="ccm-notification-help-launcher">
            <a href="#" data-help-notification-toggle="{$identifier}"><i class="fa fa-question-circle"></i></a>
        </div>
EOT;
        return $html;
    }

    public function getMessageHtml(Message $message)
    {
        $identifier = $message->getIdentifier();
        $content = $message->getContent();
        $ok = t('Ok');
        $hideAll = t('Hide All');
        $html =<<<EOT
        <div class="ccm-notification-help ccm-notification" data-help-notification="{$identifier}">
            <i class="ccm-notification-icon fa fa-info-circle"></i>
            <div class="ccm-notification-inner dialog-help">{$content}</div>
            <div class="ccm-notification-actions">
                <a href="#" data-help-notification-identifier="{$identifier}" class="ccm-notification-actions-dismiss-single" data-dismiss="help-single">{$ok}</a>
                <!--<a href="#" data-help-notification-identifier="{$identifier}" data-dismiss="help-all">{$hideAll}</a>//-->
            </div>
        </div>
EOT;
        return $html;
    }
}
