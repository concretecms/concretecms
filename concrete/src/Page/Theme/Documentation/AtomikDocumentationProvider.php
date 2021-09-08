<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Entity\Board\Board;
use Concrete\Theme\Atomik\PageTheme;
use Doctrine\ORM\EntityManager;

class AtomikDocumentationProvider implements DocumentationProviderInterface
{

    /**
     * @var PageTheme
     */
    protected $theme;

    public function __construct(PageTheme $theme)
    {
        $this->theme = $theme;
    }

    public function clearSupportingElements(): void
    {
    }

    public function installSupportingElements(): void
    {
    }

    /**
     * @return DocumentationPageInterface[]
     */
    public function getPages(): array
    {
        $pages = [
            new ThemeDocumentationPage($this->theme, 'Overview', 'overview.xml'),
        ];
        $pages = array_merge($pages, $this->theme->getDocumentationPages());
        return $pages;
    }



}
