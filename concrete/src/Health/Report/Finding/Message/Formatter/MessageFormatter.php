<?php
namespace Concrete\Core\Health\Report\Finding\Message\Formatter;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Message\Message;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;

class MessageFormatter implements FormatterInterface
{

    /**
     * @param Message $message
     * @return string
     */
    public function getFindingsListMessage(MessageInterface $message, Finding $finding): string
    {
        return $message->getMessage();
    }


}
