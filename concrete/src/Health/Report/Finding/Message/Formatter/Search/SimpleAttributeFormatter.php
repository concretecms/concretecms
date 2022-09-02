<?php

namespace Concrete\Core\Health\Report\Finding\Message\Formatter\Search;

use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Concrete\Core\Health\Report\Finding\Message\Search\SimpleAttributeMessage;
use Concrete\Core\Page\Page;

class SimpleAttributeFormatter implements FormatterInterface
{

    /**
     * @param SimpleAttributeMessage $message
     * @return string
     */
    public function getFindingsListMessage(MessageInterface $message): string
    {
        $value = $message->getCategoryValue();
        $key = $value->getAttributeKey();
        $message = '';

        if ($value instanceof PageValue) {
            $page = Page::getByID($value->getPageID());
            $message = t(
                'Page attribute "%s" on page %s (ID %s)',
                $key->getAttributeKeyDisplayName('text'),
                $page->getCollectionName(),
                $value->getPageID()
            );
        } elseif ($value instanceof ExpressValue) {
            $entry = $value->getEntry();
            $entity = $entry->getEntity();
            $message = t(
                '"%s" attribute in %s object "%s" (ID %s)',
                $key->getAttributeKeyDisplayName('text'),
                $entity->getEntityDisplayName('text'),
                $entry->getLabel(),
                $entry->getPublicIdentifier(),
            );
        }

        return $message;
    }


}
