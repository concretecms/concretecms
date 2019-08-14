<?php
namespace Concrete\Core\StyleCustomizer\Inline;

abstract class CustomStyle
{
    public function getStyleSet()
    {
        return $this->set;
    }

    /**
     * @since 5.7.5
     */
    abstract public function getStyleWrapper($css);

    abstract public function getCSS();
    abstract public function getContainerClass();
    /**
     * @since 5.7.5.3
     */
    abstract public function getCustomStyleClass();
}
