<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

/**
 * @since 5.7.4
 */
interface FormatterInterface
{
    public function getLauncherHtml(Message $message);
    public function getMessageHtml(Message $message);
}
