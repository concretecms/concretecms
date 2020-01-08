<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Controller\Panel\Multilingual;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
use Concrete\Core\Multilingual\Page\Section\Processor\MultilingualProcessorTarget;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Command\RescanMultilingualPageBatchProcessFactory;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Foundation\Queue\Batch\Processor;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die("Access Denied.");

class Copy extends DashboardSitePageController
{
    public function view()
    {
        $this->set('locales', $this->getSite()->getLocales());
    }

    public function tree_copied()
    {
        $this->set('message', t('Multilingual tree copied. You might consider rescanning links in the section you copied to.'));
        $this->view();
    }

    public function links_rescanned()
    {
        $this->set('message', t('Multilingual tree links rescanned.'));
        $this->view();
    }

    public function rescan_locale()
    {
        if ($this->token->validate('rescan_locale')) {
            $u = $this->app->make(User::class);
            if ($u->isSuperUser()) {
                $queue = $this->app->make(QueueService::class);
                $q = $queue->get('rescan_multilingual_page');
                $section = Section::getByID($_REQUEST['locale']);
                $factory = new RescanMultilingualPageBatchProcessFactory();
                $processor = $this->app->make(Processor::class);
                return $processor->process($factory, $section);
            }
        }
    }
}
