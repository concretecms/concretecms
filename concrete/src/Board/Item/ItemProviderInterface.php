<?php
namespace Concrete\Core\Board\Item;

use Concrete\Core\Entity\Board\Item;

defined('C5_EXECUTE') or die("Access Denied.");

interface ItemProviderInterface
{

    /**
     * @return Item
     */
    public function getItem():? Item;

}
