<?php
namespace Concrete\Core\Health\Report\Finding\Message\Formatter;

use Concrete\Core\Health\Report\Finding\Message\MessageInterface;

interface FormatterInterface
{

    public function getFindingsListMessage(MessageInterface $message): string;

}
