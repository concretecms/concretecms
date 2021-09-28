<?php
namespace Concrete\Core\Page\View\Preview;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Page\Theme\Theme;

class PageDesignPreviewRequest extends SkinPreviewRequest
{

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Theme
     */
    protected $theme;


    /**
     * @return Template
     */
    public function getPageTemplate(): ?Template
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setPageTemplate(Template $template): void
    {
        $this->template = $template;
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
