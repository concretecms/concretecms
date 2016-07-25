<?php
namespace Concrete\Core\Search\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Node\Type\SearchPreset;

class SavedSearchMenu extends Menu
{

    public function __construct(SearchPreset $node)
    {
        parent::__construct();

        $this->addItem(new DeleteItem($node));

    }
}
