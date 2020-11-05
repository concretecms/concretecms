<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Concrete\Core\Logging;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(LogEntry $logEntry)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/ccm/system/dialogs/logs/delete")->setQuery(["item" => $logEntry->getId()]),
                t('Delete'),
                [
                    'class' => 'dialog-launch',
                    'dialog-title' => t('Delete')
                ]
            )
        );
    }
}
