<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Concrete\Core\User;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Permission\Checker;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(UserInfo $user)
    {
        parent::__construct();

        $permissionChecker = new Checker($user);
        $permissionCheckerResponse = $permissionChecker->getResponseObject();

        if ($permissionCheckerResponse->validate("edit_user_properties")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to("/dashboard/users/search/edit", $user->getUserID()),
                    t('Edit')
                )
            );
        }

    }
}
