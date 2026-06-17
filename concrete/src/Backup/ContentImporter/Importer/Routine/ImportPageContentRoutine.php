<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use SimpleXMLElement;

class ImportPageContentRoutine extends AbstractPageContentRoutine implements SpecifiableHomePageRoutineInterface
{
    /**
     * @var \Concrete\Core\Page\Page|null
     */
    protected $home;

    /**
     * @var \Concrete\Core\Entity\Site\Site|null
     */
    private $site;

    public function getHandle()
    {
        return 'page_content';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\SpecifiableHomePageRoutineInterface::setHomePage()
     */
    public function setHomePage($c)
    {
        $this->home = $c;
    }

    public function import(SimpleXMLElement $sx)
    {
        if (!isset($sx->pages) || !isset($sx->pages->page)) {
            return;
        }
        if (!$this->home || $this->home->isError()) {
            $this->home = Page::getByID(Page::getHomePageID(), 'RECENT');
            if (!$this->home || $this->home->isError()) {
                throw new UserMessageException(t('Unable to find the home page'));
            }
        }
        $this->site = $this->home->getSite();
        $pageAttributeCategory = app(PageCategory::class);
        foreach ($sx->pages->page as $pageElement) {
            $page = $this->getPageByPath((string) $pageElement['path']);
            if (isset($pageElement->area)) {
                $this->importPageAreas($page, $pageElement);
            }
            if (isset($pageElement->attributes)) {
                foreach ($pageElement->attributes->children() as $attr) {
                    $handle = (string) $attr['handle'];
                    $ak = $pageAttributeCategory->getAttributeKeyByHandle($handle);
                    if ($ak) {
                        $value = $ak->getController()->importValue($attr);
                        $page->setAttribute($handle, $value);
                    }
                }
            }
            $hrefLangMap = $this->getHrefLangMap($pageElement);
            if ($hrefLangMap !== []) {
                $this->applyHrefLangMap($page, $hrefLangMap);
            }
            $page->reindex();
        }
    }

    /**
     * @return array keys are the destination locale ID, values are the path of the destination page
     */
    private function getHrefLangMap(SimpleXMLElement $parentElement)
    {
        if (!isset($parentElement->hreflang) || !isset($parentElement->hreflang->alternate)) {
            return [];
        }
        $map = [];
        foreach ($parentElement->hreflang->alternate as $alternateElement) {
            $locale = (string) $alternateElement['locale'];
            if ($locale === '') {
                continue;
            }
            $path = (string) $alternateElement['path'];
            if ($path === '') {
                continue;
            }
            $map[$locale] = $path;
        }

        return $map;
    }

    private function applyHrefLangMap(Page $sourcePage, array $map)
    {
        app('multilingual/detector')->assumeEnabled();
        foreach ($map as $destinationLocaleID => $destinationPagePath) {
            $destinationPage = Page::getByPath($destinationPagePath);
            if (!$destinationPage || $destinationPage->isError() || $destinationPage->getCollectionID() == $sourcePage->getCollectionID()) {
                continue;
            }
            $destinationSection = Section::getByID($destinationPage->getCollectionID());
            if (!$destinationSection || $destinationSection->isError()) {
                $destinationSection = Section::getBySectionOfSite($destinationPage);
                if (!$destinationSection || $destinationSection->isError()) {
                    continue;
                }
            }
            if ($destinationSection->getLocale() !== $destinationLocaleID) {
                continue;
            }
            if (!Section::isAssigned($sourcePage)) {
                Section::registerPage($sourcePage);
            }
            Section::relatePage($sourcePage, $destinationPage, $destinationSection->getLocale());
        }
    }

    /**
     * @return \Concrete\Core\Page\Page|null
     */
    private function getPageByPath($path)
    {
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            return $this->home;
        }
        $page = Page::getByPath($path, 'RECENT', $this->site);
        if ($page && !$page->isError()) {
            return $page;
        }
        $page = Page::getByPath($path, 'RECENT');
        if ($page && !$page->isError()) {
            return $page;
        }
        
        return null;
    }
}
