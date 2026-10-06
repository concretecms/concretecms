<?php

declare(strict_types=1);

namespace Concrete\Core\Conversation\Command;

use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die('Access Denied.');

class DeleteOrphanedConversationAttachmentsCommand extends Command
{
    /**
     * The number of seconds after which the files uploaded but not attached to a message are considered orphaned (NULL to use the conversations.attachments_orphaned_min_age configuration key).
     *
     * @var int|null
     */
    protected $minAge;

    /**
     * The maximum number of files to be deleted (NULL for no limit).
     *
     * @var int|null
     */
    protected $limit;

    /**
     * Get the number of seconds after which the files uploaded but not attached to a message are considered orphaned (NULL to use the conversations.attachments_orphaned_min_age configuration key).
     */
    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    /**
     * Set the number of seconds after which the files uploaded but not attached to a message are considered orphaned (NULL to use the conversations.attachments_orphaned_min_age configuration key).
     *
     * @return $this
     */
    public function setMinAge(?int $value): self
    {
        $this->minAge = $value;

        return $this;
    }

    /**
     * Get the maximum number of files to be deleted (NULL for no limit).
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Set the maximum number of files to be deleted (NULL for no limit).
     *
     * @return $this
     */
    public function setLimit(?int $value): self
    {
        $this->limit = $value === null ? null : max(1, $value);

        return $this;
    }
}
