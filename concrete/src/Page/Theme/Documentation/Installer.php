<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageContentRoutine;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Type\Type;

class Installer
{

    /**
     * Clears the documentation for the provided theme.
     *
     * @param Theme $theme
     */
    public function clearDocumentation(Theme $theme)
    {
        $pages = $theme->getThemeDocumentationPages();
        foreach ($pages as $page) {
            $page->delete();
        }
    }

    /**
     * Installs documentation
     *
     * @param DocumentationProviderInterface $provider
     */
    public function install(Theme $theme, DocumentationProviderInterface $provider)
    {
        $parent = Page::getByPath(THEME_DOCUMENTATION_PAGE_PATH . '/' . $theme->getThemeHandle());
        if (!$parent || ($parent && $parent->isError())) {
            $type = Type::getByHandle(THEME_DOCUMENTATION_CATEGORY_PAGE_TYPE);
            $documentation = Page::getByPath(THEME_DOCUMENTATION_PAGE_PATH);
            $parent = $documentation->add($type, ['name' => $theme->getThemeName(), 'handle' => $theme->getThemeHandle()]);
        }

        $type = Type::getByHandle(THEME_DOCUMENTATION_PAGE_TYPE);
        foreach ($provider->getPages() as $documentationPage) {
            $page = $parent->add($type, ['name' => $documentationPage->getName()]);

            $routine = new ImportPageContentRoutine();
            $routine->importPageAreas($page, $documentationPage->getContentXmlElement());
        }
    }
    
}
