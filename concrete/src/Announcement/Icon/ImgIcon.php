<?php

namespace Concrete\Core\Announcement\Icon;

class ImgIcon extends AbstractIcon
{

    /**
     * @var string
     */
    protected $src;

    public function __construct(string $src)
    {
        $this->src = $src;
    }

    public function getElement(): string
    {
        return '<img class="img-fluid" src="' . h($this->src) . '" />';
    }

}
