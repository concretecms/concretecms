<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Concrete\Core\User\Group;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Permission\Checker;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Group $group)
    {
        parent::__construct();

        $permissionChecker = new Checker($group);
        $permissionCheckerResponse = $permissionChecker->getResponseObject();

        if ($permissionCheckerResponse->validate("edit_group")) {
            $this->addItem(
                new LinkItem(
                    (string)Url::to("/dashboard/users/groups/edit", $group->getGroupID()),
                    t('Edit')
                )
            );
        }

    }
}
