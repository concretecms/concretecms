<?php

namespace Concrete\Core\Application\UserInterface\ContextMenu\Item;

use HtmlObject\Element;
use HtmlObject\Link;

class DialogLinkItem extends LinkItem
{

    protected $width;
    protected $title;
    protected $height;

    public function __construct($link, $value, $title, $width = 'auto', $height = 'auto', $attributes = array())
    {
        $attributes = array_merge($attributes, [
            'dialog-title' => $title,
            'dialog-width' => $width,
            'dialog-height' => $height,
            'class' => 'dialog-launch'
        ]);
        parent::__construct($link, $value, $attributes);
    }


}