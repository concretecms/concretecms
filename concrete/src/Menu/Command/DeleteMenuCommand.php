<?php

namespace Concrete\Core\Menu\Command;

use Concrete\Core\Foundation\Command\Command;

class DeleteMenuCommand extends Command
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
