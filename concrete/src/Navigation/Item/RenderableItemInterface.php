<?php
namespace Concrete\Core\Navigation\Item;

use HtmlObject\Traits\Tag;

interface RenderableItemInterface extends ItemInterface
{

    public function render(): Tag;

}
