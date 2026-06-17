<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter\Exception\MissingPageAtPathException;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\PagePath;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Locale\Service;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportPageStructureRoutine extends AbstractPageStructureRoutine implements SpecifiableHomePageRoutineInterface
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
        return 'page_structure';
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
        if (!isset($sx->pages)) {
            return;
        }
        $elements = [];
        foreach ($sx->pages->children() as $element) {
            $elements[] = $element;
        }
        if ($elements === []) {
            return;
        }
        if (!$this->home || $this->home->isError()) {
            $this->home = Page::getByID(Page::getHomePageID(), 'RECENT');
            if (!$this->home || $this->home->isError()) {
                throw new UserMessageException(t('Unable to find the home page'));
            }
        }
        $this->site = $this->home->getSite();
        $elements = $this->sortElementsByPath($elements);
        while ($elements !== []) {
            $delayed = [];
            $missingPageAtPathException = null;
            foreach ($elements as $element) {
                try {
                    switch ($element->getName()) {
                        case 'page':
                            $localeInfo = $this->extractLocale($element);
                            $page = $this->getOrCreatePage($element, $localeInfo);
                            $this->importAdditionalPagePaths($element, $page);
                            break;
                        case 'external-link':
                            $this->importExternalLink($element);
                            break;
                        case 'alias':
                            $this->importAlias($element);
                            break;
                    }
                } catch (MissingPageAtPathException $x) {
                    $delayed[] = $element;
                    if ($missingPageAtPathException === null) {
                        $missingPageAtPathException = $x;
                    } else {
                        $missingPageAtPathException = $missingPageAtPathException->merge($x);
                    }
                }
            }
            if (count($delayed) == count($elements)) {
                throw $missingPageAtPathException;
            }
            $elements = $delayed;
            $this->clearPageCache();
        }
    }

    private function clearPageCache()
    {
        $cache = app('cache/request');
        $cache->flush();
    }

    /**
     * @throws \Concrete\Core\Backup\ContentImporter\Exception\MissingPageAtPathException
     *
     * @return \Concrete\Core\Page\Page
     */
    private function getOrCreatePage(SimpleXMLElement $pageElement, ?array $localeInfo = null)
    {
        $package = static::getPackageObject($pageElement['package']);
        $cName = isset($pageElement['name']) ? (string) $pageElement['name'] : '';
        $cDescription = isset($pageElement['description']) ? (string) $pageElement['description'] : '';
        $cDatePublic = (string) $pageElement['public-date'];
        $pageTemplate = Template::getByHandle($pageElement['template']);
        $pageTypeHandle = isset($pageElement['pagetype']) ? (string) $pageElement['pagetype'] : '';
        $pageType = $pageTypeHandle === '' ? null : Type::getByHandle((string) $pageElement['pagetype']);

        $pathSlugs = isset($pageElement['path']) ? preg_split('{/}', (string) $pageElement['path'], -1, PREG_SPLIT_NO_EMPTY) : [];
        if ($pathSlugs === []) {
            $page = $this->home;
        } else {
            $pagePath = '/' . implode('/', $pathSlugs);
            $page = $this->getPageByPath($pagePath, 'RECENT');
        }

        if ($page !== null) {
            if ($localeInfo !== null) {
                $this->updateExistingLocale($page, $localeInfo);
            }
            $page->update([
                'cName' => $cName === '' ? null : $cName,
                'cDescription' => $cDescription,
                'cDatePublic' => $cDatePublic === '' ? null : $cDatePublic,
                'ptID' => $pageType === null ? null : $pageType->getPageTypeID(),
                'pTemplateID' => $pageTemplate === null ? null : $pageTemplate->getPageTemplateID(),
                'uID' => $this->resolveUserName($pageElement['user']),
                'pkgID' => $package === null ? null : $package->getPackageID(),
            ]);
            return $page;
        }

        $slugs = $pathSlugs;
        $cHandle = array_pop($slugs);
        if ($slugs === []) {
            $parent = $this->home;
        } else {
            $parentPagePath = '/' . implode('/', $slugs);
            $parent = $this->getPageByPath($parentPagePath);
            if ($parent === null) {
                throw new MissingPageAtPathException($parentPagePath);
            }
        }
        if ($localeInfo === null || $this->localeAlreadyExists($localeInfo)) {
            $page = $parent->add($pageType, [
                'uID' => $this->resolveUserName($pageElement['user']),
                'pkgID' => $package === null ? 0 : $package->getPackageID(),
                'cName' => $cName,
                'cHandle' => $cHandle,
                'cDescription' => $cDescription,
                'cDatePublic' => $cDatePublic === '' ? null : $cDatePublic,
            ], $pageTemplate);
            return $page;
        }

        if (!$pageTemplate) {
            throw new UserMessageException(t('Missing page template when creating the home of a language'));
        }
        app('multilingual/detector')->assumeEnabled();
        $service = app(Service::class);
        $locale = $service->add($this->home->getSite(), $localeInfo['language'], $localeInfo['country']);
        $page = $service->addHomePage($locale, $pageTemplate, $cName === '' ? 'Home' : $cName, $cHandle);
        $page->update([
            'cDescription' => $cDescription,
            'cDatePublic' => $cDatePublic === '' ? null : $cDatePublic,
            'ptID' => $pageType === null ? null : $pageType->getPageTypeID(),
            'uID' => $this->resolveUserName($pageElement['user']),
            'pkgID' => $package === null ? 0 : $package->getPackageID(),
        ]);

        return $page;
    }

    private function extractLocale(SimpleXMLElement $pageElement)
    {
        if (!isset($pageElement->locale)) {
            return null;
        }
        $localeElement = $pageElement->locale;
        $language = isset($localeElement['language']) ? (string) $localeElement['language'] : '';
        if ($language === '') {
            return null;
        }
        $country =  isset($localeElement['country']) ? (string) $localeElement['country'] : '';
        if ($country === '') {
            return null;
        }
        return [
            'language' => $language,
            'country' => $country,
        ];
    }

    private function updateExistingLocale(Page $page, array $localeInfo)
    {
        $pageTree = $page->getSiteTreeObject();
        if (!$pageTree || $pageTree->getSiteHomePageID() != $page->getCollectionID()) {
            return;
        }
        $editingLocale = $pageTree->getLocale();
        if ($editingLocale->getLanguage() === $localeInfo['language'] && $editingLocale->getCountry() === $localeInfo['country']) {
            return;
        }
        if ($this->localeAlreadyExists($localeInfo)) {
            return;
        }
        $editingLocale->setLanguage($localeInfo['language']);
        $editingLocale->setCountry($localeInfo['country']);
        $service = app(Service::class);
        $service->updatePluralSettings($editingLocale);
        $em = app(EntityManagerInterface::class);
        $em->flush();
    }

    /**
     * @return bool
     */
    private function localeAlreadyExists(array $localeInfo)
    {
        foreach ($this->home->getSite()->getLocales() as $locale) {
            if ($locale->getLanguage() === $localeInfo['language'] && $locale->getCountry() === $localeInfo['country']) {
                return true;
            }
        }

        return false;
    }

    private function importAdditionalPagePaths(SimpleXMLElement $pageElement, Page $page)
    {
        if (!isset($pageElement->{'additional-path'})) {
            return;
        }
        $em = app(EntityManagerInterface::class);
        foreach ($pageElement->{'additional-path'} as $additionalPathElement) {
            $additionalPath = '/' . trim((string) $additionalPathElement['path'], '/');
            $pagePath = new PagePath();
            $pagePath->setPagePath($additionalPath);
            $pagePath->setPageObject($page);
            $em->persist($pagePath);
        }
        $em->flush();
    }

    /**
     * @throws \Concrete\Core\Backup\ContentImporter\Exception\MissingPageAtPathException
     *
     * @return \Concrete\Core\Page\Page|null returns NULL if there's already a collection with the same handle, the newly created page otherwise
     */
    private function importExternalLink(SimpleXMLElement $externalLinkElement)
    {
        $slugs = preg_split('{/}', (string) $externalLinkElement['path'], -1, PREG_SPLIT_NO_EMPTY);
        $cHandle = array_pop($slugs);
        if ($cHandle === null) {
            throw new UserMessageException(t('Missing the path of the external link'));
        }
        $parentPagePath = '/' . implode('/', $slugs);
        $parent = $this->getPageByPath($parentPagePath);
        if ($parent === null) {
            throw new MissingPageAtPathException($parentPagePath);
        }
        if ($this->parentPageHasChildWithHandle($parent, $cHandle)) {
            return null;
        }
        $page = $parent->addExternalLink(
            (string) $externalLinkElement['name'],
            (string) $externalLinkElement['destination'],
            [
                'newWindow' => filter_var((string) $externalLinkElement['new-window'], FILTER_VALIDATE_BOOLEAN),
                'handle' => $cHandle,
                'uID' => $this->resolveUserName($externalLinkElement['user']),
            ]
        );

        return $page;
    }

    /**
     * @throws \Concrete\Core\Backup\ContentImporter\Exception\MissingPageAtPathException
     *
     * @return \Concrete\Core\Page\Page|string|null returns NULL if there's already a collection with the same handle, the newly created page otherwise
     */
    private function importAlias(SimpleXMLElement $aliasElement)
    {
        $slugs = preg_split('{/}', (string) $aliasElement['path'], -1, PREG_SPLIT_NO_EMPTY);
        $cHandle = array_pop($slugs);
        if ($cHandle === null) {
            throw new UserMessageException(t('Missing the path of the external link'));
        }
        $parentPagePath = '/' . implode('/', $slugs);
        $parentPage = $this->getPageByPath($parentPagePath);
        if ($parentPage === null) {
            throw new MissingPageAtPathException($parentPagePath);
        }
        if ($this->parentPageHasChildWithHandle($parentPage, $cHandle)) {
            return null;
        }
        $originalPagePath = '/' . trim((string) $aliasElement['original-path'], '/');
        $originalPage = $this->getPageByPath($originalPagePath);
        if ($originalPage === null) {
            throw new MissingPageAtPathException($originalPagePath);
        }
        $alias = $originalPage->createAlias($parentPage, [
            'name' => (string) $aliasElement['name'],
            'handle' => $cHandle,
            'uID' => $this->resolveUserName($aliasElement['user']),
        ]);

        return $alias;
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

    /**
     * @param string $childHandle
     *
     * @return bool
     */
    private function parentPageHasChildWithHandle(Page $parentPage, $childHandle)
    {
        $cn = app(Connection::class);
        $cID = $cn->fetchColumn(
            <<<'EOT'
SELECT
    Pages.cID
FROM
    Pages
    INNER JOIN Collections ON Pages.cID = Collections.cID
WHERE
    Pages.cParentID = :parentPageID
    AND Collections.cHandle = :childHandle
LIMIT 1
EOT
            ,
            [
                'parentPageID' => $parentPage->getCollectionID(),
                'childHandle' => $childHandle,
            ]
        );

        return $cID ? true : false;
    }
}
