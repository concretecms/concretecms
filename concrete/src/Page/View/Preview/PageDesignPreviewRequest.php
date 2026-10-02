<?php
namespace Concrete\Core\Page\View\Preview;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Page\Theme\Theme;

class PageDesignPreviewRequest extends SkinPreviewRequest
{

    /**
     * @var Template|null
     */
    protected $template;

    /**
     * @var Theme|null
     */
    protected $theme;


    public function getPageTemplate(): ?Template
    {
        return $this->template;
    }

    public function setPageTemplate(Template $template): void
    {
        $this->template = $template;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }



}
