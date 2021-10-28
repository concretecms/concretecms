<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

/**
 * @deprecated
 */
class Formatter implements FormatterInterface
{
    public function getLauncherHtml(Message $message)
    {
        return '';
    }

    public function getMessageHtml(Message $message)
    {
        return '';
    }
}
