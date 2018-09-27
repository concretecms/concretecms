<?php

namespace Concrete\Core\Feed;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use URL;

abstract class FeedUsageOnPageEntry implements FeedUsageEntryInterface
{
    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $page;

    /**
     * Create a new instance.
     *
     * @param int $pageID
     * @param int $pageVersion
     *
     * @return static|null
     */
    public static function create($pageID, $pageVersion)
    {
        $page = Page::getByID($pageID, $pageVersion);
        if (!$page || $page->isError()) {
            return null;
        }
        $result = new static();
        $result->page = $page;

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Feed\FeedUsageEntryInterface::toHtml()
     */
    public function toHtml()
    {
        $pageVersion = $this->page->getVersionObject();
        $url = '';
        $notes = $pageVersion->isApproved() ? t('approved version "%s"', $pageVersion->getVersionComments() ?: $pageVersion->getVersionID()) : t('not approved version "%s"', $pageVersion->getVersionComments() ?: $pageVersion->getVersionID());
        if ($this->page->getPageTypeHandle() == STACKS_PAGE_TYPE) {
            $stack = Stack::getByID($this->page->getCollectionID(), $pageVersion->getVersionID());
            if ($stack->getStackType() === Stack::ST_TYPE_GLOBAL_AREA) {
                $name = t('Global Area "%s"', t($stack->getStackName()));
            } else {
                $name = t('Stack "%s"', t($stack->getStackName()));
            }
        } elseif ($this->page->isMasterCollection()) {
            $name = t('Page Template "%s"', t($this->page->getPageTypeObject()->getPageTypeName()));
        } else {
            $name = $this->page->getCollectionName();
            $url = (string) URL::to($this->page);
        }
        $result = '';
        if ($url !== '') {
            $result .= '<a href="' . h($url) . '">';
        }
        $result .= h($name);
        if ($url !== '') {
            $result .= '</a>';
        }
        if ($notes !== '') {
            $result .= ' (' . h($notes) . ')';
        }

        return $result;
    }
}
