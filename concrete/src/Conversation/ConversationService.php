<?php

namespace Concrete\Core\Conversation;

use Concrete\Block\CoreConversation\Controller;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Block\Block;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;

/**
 * Class ConversationService
 * Currently manages tracking review aggregates
 */
class ConversationService
{

    /** @var Connection */
    protected $connection;

    /** @var PageCategory  */
    protected $pageCategory;

    /** @var DatabaseManager */
    private $manager;

    /**
     * ConversationService constructor.
     * @param PageCategory $category
     * @param DatabaseManager $manager
     */
    public function __construct(PageCategory $category, DatabaseManager $manager)
    {
        $this->pageCategory = $category;
        $this->manager = $manager;
    }

    /**
     * Track a review on a message
     * @param \Concrete\Core\Conversation\Message\Message $message
     * @param \Concrete\Core\Block\Block $block
     */
    public function trackReview(Message $message, Block $block)
    {
        $average = $this->fetchConversationAverage($message->getConversationID());
        $this->assignAverage($block, $average);
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
            $this->connection = $this->manager->connection();
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

        $key = $this->pageCategory->getAttributeKeyByID($controller->reviewAggregateAttributeKey);

        $collection = $block->getBlockCollectionObject();
        $collection->getVersionObject()->setAttribute($key, (100/5) * $average);
    }

}
