<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation\Command;

use Concrete\Core\Foundation\Command\Command;

class UpdateDashboardMenuCommand extends Command
{

    /**
     * @var string
     */
    protected $menuId;

    public function __construct(string $menuId)
    {
        $this->menuId = $menuId;
    }

    /**
     * @return string
     */
    public function getMenuId(): string
    {
        return $this->menuId;
    }




}
