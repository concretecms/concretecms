<?php
namespace Concrete\Core\Health\Report\Finding\Message;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Filesystem\Element;

interface MessageHasDetailsInterface
{

    public function getDetailsElement(MessageInterface $message, Finding $finding): Element;

    public function getDetailsString(MessageInterface $message): string;
}
