<?php
namespace Concrete\Core\Page\View\Preview;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class SkinPreviewRequest
{

    /**
     * @var SkinInterface
     */
    protected $skin;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * If we're previewing a custom skin's modification slike setting a new color on a link, changing a font family,
     * we're going to have customCss set in this parameter
     *
     * @var string|null
     */
    protected $customCss;
    
    /**
     * @return SkinInterface
     */
    public function getSkin(): SkinInterface
    {
        return $this->skin;
    }

    /**
     * @param SkinInterface $skin
     */
    public function setSkin(SkinInterface $skin): void
    {
        $this->skin = $skin;
    }

    /**
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return string|null
     */
    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    /**
     * @param string|null $customCss
     */
    public function setCustomCss(?string $customCss): void
    {
        $this->customCss = $customCss;
    }







}
