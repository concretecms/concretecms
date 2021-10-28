<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Concrete\Core\Page;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Permission\Checker;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Page $page)
    {
        parent::__construct();

        $permissionChecker = new Checker($page);
        $permissionCheckerResponse = $permissionChecker->getResponseObject();

        if ($permissionCheckerResponse->validate("view_page")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to($page),
                    t('Visit')
                )
            );
        }

        $this->addItem(new DividerItem());

        if ($permissionCheckerResponse->validate("edit_page_properties")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/dialogs/page/seo')->setQuery(["cID" => $page->getCollectionID()]),
                    t('SEO'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('SEO')
                    ]
                )
            );
        }

        if ($permissionCheckerResponse->validate("edit_page_properties")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/dialogs/page/location')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Location'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Location')
                    ]
                )
            );
        }

        $this->addItem(new DividerItem());

        if ($permissionCheckerResponse->validate("edit_page_properties")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/dialogs/page/attributes')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Attributes'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Attributes')
                    ]
                )
            );
        }

        if ($permissionCheckerResponse->validate("edit_page_speed_settings")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/panels/details/page/caching')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Caching'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Caching')
                    ]
                )
            );
        }

        if ($permissionCheckerResponse->validate("edit_page_permissions")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/panels/details/page/permissions')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Permissions'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Permissions')
                    ]
                )
            );
        }

        if ($permissionCheckerResponse->validate("edit_page_page_type")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/dialogs/page/design')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Design & Type'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Design & Type')
                    ]
                )
            );
        }

        if ($permissionCheckerResponse->validate("approve_page_versions")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/panels/page/versions')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Versions'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Versions')
                    ]
                )
            );
        }

        if ($permissionCheckerResponse->validate("delete_page")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to('/ccm/system/dialogs/page/delete_from_sitemap')->setQuery(["cID" => $page->getCollectionID()]),
                    t('Delete'),
                    [
                        'class' => 'dialog-launch',
                        'dialog-title' => t('Delete')
                    ]
                )
            );
        }
    }
}
