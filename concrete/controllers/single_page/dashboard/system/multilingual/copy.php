<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Controller\Panel\Multilingual;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Command\RescanMultilingualPageCommand;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Stack\StackList;
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

                $section = Section::getByID($_REQUEST['locale']);
                $pages = $section->populateRecursivePages(
                    [],
                    [
                        'cID' => $section->getCollectionID(), ],
                    $section->getCollectionParentID(),
                    0,
                    false
                );

                // Add in all the stack pages found for the current locale.
                $list = new StackList();
                $list->filterByLanguageSection($section);
                $results = $list->get();
                foreach ($results as $result) {
                    $pages[] = ['cID' => $result->getCollectionID()];
                }

                $commands = [];
                foreach ($pages as $page) {
                    $commands[] = new RescanMultilingualPageCommand($page['cID']);
                }

                $batch = Batch::create(t('Rescan Pages'), $commands);
                return $this->dispatchBatch($batch);
            }
        }
    }
}
