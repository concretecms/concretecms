<?php

namespace Concrete\Core\Announcement\Icon;

class SvgIcon extends AbstractIcon
{

    /**
     * @var string
     */
    protected $rawSvg;

    public function __construct(string $rawSvg)
    {
        $this->rawSvg = $rawSvg;
    }

    public function getElement(): string
    {
        return $this->rawSvg;
    }

}
