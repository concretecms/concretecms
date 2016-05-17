<?php
namespace Concrete\Core\StyleCustomizer\Inline;

abstract class CustomStyle
{
    public function getStyleSet()
    {
        return $this->set;
    }

    abstract public function getStyleWrapper($css);

    abstract public function getCSS();
    abstract public function getContainerClass();
    abstract public function getCustomStyleClass();
}
