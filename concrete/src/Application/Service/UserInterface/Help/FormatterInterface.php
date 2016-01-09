<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

interface FormatterInterface
{
    public function getLauncherHtml(Message $message);
    public function getMessageHtml(Message $message);
}