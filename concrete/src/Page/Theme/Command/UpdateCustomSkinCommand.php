<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;

class UpdateCustomSkinCommand extends Command
{

    /**
     * @var CustomSkin
     */
    protected $customSkin;

    /**
     * The styles data as posted by the customizer
     *
     * @var array
     */
    protected $styles;

    /**
     * @var string
     */
    protected $customCss;

    /**
     * @return CustomSkin
     */
    public function getCustomSkin(): CustomSkin
    {
        return $this->customSkin;
    }

    /**
     * @param CustomSkin $customSkin
     */
    public function setCustomSkin(CustomSkin $customSkin): void
    {
        $this->customSkin = $customSkin;
    }

    /**
     * @return string
     */
    public function getCustomCss(): string
    {
        return $this->customCss;
    }

    /**
     * @param string $customCss
     */
    public function setCustomCss(string $customCss): void
    {
        $this->customCss = $customCss;
    }

    /**
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     */
    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }


}
