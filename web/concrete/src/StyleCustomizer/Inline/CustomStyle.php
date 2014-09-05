<?php
namespace Concrete\Core\StyleCustomizer\Inline;
abstract class CustomStyle
{

    public function getStyleSet()
    {
        return $this->set;
    }

    abstract public function getCSS();
    abstract public function getContainerClass();

}
