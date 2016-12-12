<?php

namespace Concrete\Core\Page\Relation\Formatter;

use Concrete\Core\Entity\Page\Relation\Relation;

class SiblingFormatter implements FormatterInterface
{

    protected $relation;

    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function getDisplayName()
    {
        $page = $this->relation->getPageObject();
        $title = sprintf('%s > %s', $page->getSiteTreeObject()->getDisplayName(), $page->getCollectionName());
        return $title;
    }
}
