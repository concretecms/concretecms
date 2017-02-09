<?php

namespace Concrete\Core\Conversation;

use Concrete\Block\CoreConversation\Controller;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Block\Block;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Database\Connection\Connection;

class ConversationService implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    protected $connection;

    public function trackReview(Message $message)
    {
        $blockID = $this->fetchBlockIDFromMessage($message);
        $average = $this->fetchConversationAverage($message->getConversationID());

        $block = Block::getByID($blockID);
        $this->assignAverage($block, $average);
    }

    private function fetchBlockIDFromMessage(Message $message)
    {
        $conversation = $message->getConversationID();
        $qb = $this->getConnection()->createQueryBuilder();

        $conversationBlockType = new Controller();
        $result = $qb->select('bID')
            ->from($conversationBlockType->getBlockTypeDatabaseTable())
            ->where('cnvID=:id')
            ->setParameter(':id', $conversation)
            ->execute();

        return $result->fetchColumn();
    }

    private function fetchConversationAverage($conversationID)
    {
        $qb = $this->getConnection()->createQueryBuilder();

        $result = $qb->select('AVG(cnvMessageReview)')
            ->from('ConversationMessages')
            ->where('cnvID = :cnvID')
            ->andWhere('cnvMessageLevel = 0')
            ->andWhere('cnvMessageReview > 0')
            ->setParameter(':cnvID', $conversationID)
            ->execute();

        return $result->fetchColumn();
    }

    /**
     * @return \Concrete\Core\Database\Connection\Connection
     */
    private function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->app->make(Connection::class);
        }

        return $this->connection;
    }

    private function assignAverage(Block $block, $average)
    {
        /** @var Controller $controller */
        $controller = $block->getController();
        if (!$controller->enableTopCommentReviews || !$controller->reviewAggregateAttributeKey) {
            // No attribute key configured
            return;
        }

        $category = $this->app->make(PageCategory::class);
        $key = $category->getAttributeKeyByID($controller->reviewAggregateAttributeKey);

        $collection = $block->getBlockCollectionObject();
        $collection->getVersionObject()->setAttribute($key, (100/5) * $average);
    }

}
