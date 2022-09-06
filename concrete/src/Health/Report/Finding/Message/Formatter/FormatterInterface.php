<?php
namespace Concrete\Core\Health\Report\Finding\Message\Formatter;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;

interface FormatterInterface
{

    public function getFindingsListMessage(MessageInterface $message, Finding $finding): string;

}
