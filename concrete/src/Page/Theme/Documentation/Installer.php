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
    public function clearDocumentation(Theme $theme, DocumentationProviderInterface $provider)
    {
        $provider->clearSupportingElements();
        $documentationPage = $theme->getThemeDocumentationParentPage();
        if ($documentationPage) {
            $documentationPage->delete();
        }
    }

    /**
     * Installs documentation
     *
     * @param DocumentationProviderInterface $provider
     */
    public function install(Theme $theme, DocumentationProviderInterface $provider)
    {
        $provider->installSupportingElements();
        $parent = Page::getByPath(THEME_DOCUMENTATION_PAGE_PATH . '/' . $theme->getThemeHandle());
        if (!$parent || ($parent && $parent->isError())) {
            $type = Type::getByHandle(THEME_DOCUMENTATION_CATEGORY_PAGE_TYPE);
            $documentation = Page::getByPath(THEME_DOCUMENTATION_PAGE_PATH);
            $parent = $documentation->add($type, ['name' => $theme->getThemeName(), 'cHandle' => $theme->getThemeHandle()]);
            $parent->setTheme($theme);
        }

        foreach ($provider->getPages() as $documentationPage) {
            $newDocumentationPage = $documentationPage->installDocumentationPage($parent);
            $newDocumentationPage->setTheme($theme);

        }
        $provider->finishInstallation();
    }
    
}
