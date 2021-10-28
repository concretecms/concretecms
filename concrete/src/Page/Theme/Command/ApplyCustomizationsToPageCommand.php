<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Page\Page;

/**
 * This command is used by the legacy non-skin-based customizer.
 * @deprecated – use the skin based customizer instead.
 *
 */
class ApplyCustomizationsToPageCommand extends ApplyCustomizationsToSiteCommand
{

    /**
     * @var Page
     */
    protected $page;

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
    }






}
