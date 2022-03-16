<?php
namespace Concrete\Core\Page\Theme;

use Concrete\Core\StyleCustomizer\Customizer\Type\TypeInterface;

interface CustomizableInterface
{

    public function getThemeCustomizerType(): TypeInterface;

}
