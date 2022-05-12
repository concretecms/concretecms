<?php

namespace Concrete\Block\DesktopDraftList;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\UserInfo;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string
     */
    protected $btTable = 'btDesktopDraftList';

    /**
     * @var int
     */
    protected $defaultDraftsPerPage = 10;

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [Features::DESKTOP];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays a list of all drafts.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Draft List');
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->set('defaultDraftsPerPage', $this->defaultDraftsPerPage);
    }

    /**
     * @return void
     */
    public function edit()
    {
        $this->set('defaultDraftsPerPage', $this->defaultDraftsPerPage);
    }

    /**
     * @param array<string,mixed> $args
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool|\Concrete\Core\Error\ErrorList\ErrorList|mixed|object
     */
    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        if (!empty($args['draftsPerPage'])) {
            $numbersValidation = $this->app->make('helper/validation/numbers');
            if (!$numbersValidation->integer($args['draftsPerPage'])) {
                $e->add(t('You must specify an integer value for the number of drafts per page.'));
            }
        }

        return $e;
    }

    /**
     * @param array<string,mixed> $args
     *
     * @return void
     */
    public function save($args)
    {
        if (empty($args['draftsPerPage'])) {
            $args['draftsPerPage'] = $this->defaultDraftsPerPage;
        }
        parent::save($args);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $myDrafts = [];
        $pagination = null;
        $site = $this->app->make('site')->getSite();
        if (is_object($site)) {
            $draftsParentPage = Page::getDraftsParentPage($site);
            if (is_object($draftsParentPage) && !empty($draftsParentPage->getCollectionID())) {
                $list = new PageList();
                $list->setNameSpace('b' . $this->bID);
                $list->filterByParentID($draftsParentPage->getCollectionID());
                $list->includeSystemPages();
                $list->includeInactivePages();
                $list->sortBy('cDateAdded', 'desc');
                $list->setPageVersionToRetrieve($list::PAGE_VERSION_RECENT);
                $list->setItemsPerPage((!empty($this->draftsPerPage)) ? $this->draftsPerPage : $this->defaultDraftsPerPage);
                $pagination = $list->getPagination();
                $drafts = $pagination->getCurrentPageResults();
            }
        }

        if (!empty($drafts)) {
            $date = $this->app->make('helper/date');
            $navigation = $this->app->make('helper/navigation');
            foreach ($drafts as $draft) {
                $dp = new Checker($draft);
                /** @phpstan-ignore-next-line */
                if ($dp->canEditPageContents()) {
                    $draftName = $draft->getCollectionName();
                    if (empty($draftName)) {
                        $draftName = t('(Untitled)');
                    }
                    $draftUser = null;
                    $draftUserID = $draft->getCollectionUserID();
                    if (!empty($draftUserID)) {
                        $user = UserInfo::getByID($draftUserID);
                        if (is_object($user)) {
                            $draftUser = $user->getUserName();
                        }
                    }
                    $deleteLink = null;
                    /** @phpstan-ignore-next-line */
                    if ($dp->canDeletePage()) {
                        $deleteLink = \Concrete\Core\Support\Facade\Url::to('/ccm/system/dialogs/page/delete_from_sitemap') . '?cID=' . $draft->getCollectionID();
                    }
                    $myDrafts[] = [
                        'link' => $navigation->getLinkToCollection($draft),
                        'id' => $draft->getCollectionID(),
                        'name' => $draftName,
                        'dateAdded' => $date->formatDateTime($draft->getCollectionDateAdded(), false),
                        'user' => $draftUser,
                        'deleteLink' => $deleteLink,
                    ];
                }
            }
        }
        $this->set('drafts', $myDrafts);
        $this->set('pagination', $pagination);
    }

    /**
     * @return void
     */
    public function action_reload_drafts()
    {
        $b = $this->getBlockObject();
        $bv = new BlockView($b);
        $bv->render('view');
        $this->app->shutdown();
    }
}
