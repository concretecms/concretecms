<?php

namespace Concrete\Core\Menu\Command;

use Concrete\Core\Foundation\Command\Command;

class UpdateMenuCommand extends Command
{

    /**
     * @var string
     */
    protected $menuId;

    /**
     * @var string
     */
    protected $name;

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }



    

}
