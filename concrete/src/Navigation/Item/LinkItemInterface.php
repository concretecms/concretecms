<?php
namespace Concrete\Core\Navigation\Item;

use HtmlObject\Traits\Tag;

interface LinkItemInterface extends ItemInterface
{

    /**
     * @return string
     */
    public function getName(): string;


    /**
     * @return string
     */
    public function getUrl(): string;


}
