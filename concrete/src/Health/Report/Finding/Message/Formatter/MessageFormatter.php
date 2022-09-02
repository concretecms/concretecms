<?php
namespace Concrete\Core\Health\Report\Finding\Message\Formatter;

use Concrete\Core\Health\Report\Finding\Message\Message;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;

class MessageFormatter implements FormatterInterface
{

    /**
     * @param Message $message
     * @return string
     */
    public function getFindingsListMessage(MessageInterface $message): string
    {
        return $message->getMessage();
    }


}
