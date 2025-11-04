<?php

namespace Concrete\Core\Page;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Page\Type\Composer\Control\Control as PageTypeComposerControl;
use Concrete\Core\Page\Type\Type;

class DraftService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_PAGES;
    }

    public function createDraft(Type $type, Template $template, ?Site $site = null): Page
    {
        $parent = Page::getDraftsParentPage($site);
        $data = ['cvIsApproved' => 0, 'cIsDraft' => 1, 'cIsActive' => false, 'cAcquireComposerOutputControls' => true];
        $p = $parent->add($type, $data, $template);

        // now we setup in the initial configurated page target
        $target = $type->getPageTypePublishTargetObject();
        $cParentID = $target->getDefaultParentPageID();
        if ($cParentID > 0) {
            $p->setPageDraftTargetParentPageID($cParentID);
        }

        $controls = PageTypeComposerControl::getList($type);
        foreach ($controls as $cn) {
            $cn->onPageDraftCreate($p);
        }
        $this->logger->info(t('Created new page draft (%s) of type %s', $p->getCollectionID(), $type->getPageTypeHandle()));
        return $p;
    }
}