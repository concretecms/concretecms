<?php

namespace Concrete\Core\Health\Report\Finding\Message\Formatter\Search;

use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Health\Report\Finding\Control\Location;
use Concrete\Core\Health\Report\Finding\Control\LocationInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageHasDetailsInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Concrete\Core\Health\Report\Finding\Message\Search\BlockMessage;

class BlockFormatter implements FormatterInterface, MessageHasDetailsInterface, HasLocationInterface
{

    /**
     * @return string
     */
    public function getFindingsListMessage(MessageInterface $findingMessage, Finding $finding): string
    {
        if (!$findingMessage instanceof BlockMessage) {
            throw new \InvalidArgumentException(t('The message must be an instance of %s.', BlockMessage::class));
        }
        $block = Block::getByID($findingMessage->getBlockID());
        if ($block) {
            $page = $block->getBlockPageObject();
            if ($page !== null) {
                $message = t(
                    '%s block type (ID %s) on page %s (ID %s)',
                    $block->getBlockTypeName(),
                    $block->getBlockID(),
                    $page->getCollectionPath(),
                    $page->getCollectionID()
                );
            } else {
                $message = t('%s block type (ID %s)', $block->getBlockTypeHandle(), $block->getBlockID());
            }
        } else {
            $message = t('Unknown block type. Perhaps this has already been deleted.');
        }
        return $message;
    }

    /**
     * @param BlockMessage $message
     * @param Finding $finding
     * @return Element
     */
    public function getDetailsElement(MessageInterface $message, Finding $finding): Element
    {
        return new Element(
            'dashboard/health/report/finding/message/search/details', [
            'details' => $this->getDetailsString($message),
            'result' => $finding->getResult()
            ]
        );
    }

    public function getDetailsString(MessageInterface $message): string
    {
        if (!$message instanceof BlockMessage) {
            throw new \InvalidArgumentException(t('The message must be an instance of %s.', BlockMessage::class));
        }

        return $message->getContent();
    }


    /**
     * @return LocationInterface|null
     */
    public function getLocation(MessageInterface $message): ?LocationInterface
    {
        if (!$message instanceof BlockMessage) {
            throw new \InvalidArgumentException(t('The message must be an instance of %s.', BlockMessage::class));
        }
        $block = Block::getByID($message->getBlockID());
        if ($block) {
            $page = $block->getBlockPageObject();
            if ($page !== null) {
                return new Location($page->getCollectionLink(), t('View Page'));
            }
        }
        return null;
    }

}
