<?php
namespace Concrete\Block\DesktopDraftList;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\User\UserInfo;
use Permissions;
use URL;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController
{

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;

    public function getBlockTypeDescription()
    {
        return t('Displays a list of all drafts.');
    }

    public function getBlockTypeName()
    {
        return t('Draft List');
    }

    public function view()
    {
        $myDrafts = [];
        $site = $this->app->make('site')->getSite();
        if (is_object($site)) {
            $drafts = Page::getDrafts($site);
            $date = $this->app->make('helper/date');
            $navigation = $this->app->make('helper/navigation');
            foreach ($drafts as $draft) {
                $dp = new Permissions($draft);
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
                    if ($dp->canDeletePage()) {
                        $deleteLink = URL::to('/ccm/system/dialogs/page/delete') . '?cID=' . $draft->getCollectionID();
                    }
                    $myDrafts[] = [
                        'link' => $navigation->getLinkToCollection($draft),
                        'id' => $draft->getCollectionID(),
                        'name' => $draftName,
                        'dateAdded' => $date->formatDateTime($draft->getCollectionDateAdded(), false),
                        'user' => $draftUser,
                        'deleteLink' => $deleteLink
                    ];
                }
            }
        }
        $this->set('drafts', $myDrafts);
    }
}
