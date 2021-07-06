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





}
