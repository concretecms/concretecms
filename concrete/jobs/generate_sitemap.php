<?php
namespace Concrete\Job;

use Concrete\Core\Cache\Cache;
use Config;
use Job as AbstractJob;
use Core;
use Database;
use PermissionKey;
use Group;
use DateTime;
use CollectionAttributeKey;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;
use SimpleXMLElement;
use Page;
use Events;

class GenerateSitemap extends AbstractJob
{
    /** The end-of-line terminator.
     * @var string
     */
    const EOL = "\n";

    /** Returns the job name.
     * @return string
     */
    public function getJobName()
    {
        return t('Generate the sitemap.xml file');
    }

    /** Returns the job description.
     * @return string
     */
    public function getJobDescription()
    {
        return t('Generate the sitemap.xml file that search engines use to crawl your site.');
    }

    /** Executes the job.
     * @throws \Exception Throws an exception in case of errors.
     *
     * @return string Returns a string describing the job result in case of success.
     */
    public function run()
    {
        Cache::disableAll();
        try {
            $instances = array(
                'navigation' => Core::make('helper/navigation'),
                'dashboard' => Core::make('helper/concrete/dashboard'),
                'view_page' => PermissionKey::getByHandle('view_page'),
                'guestGroup' => Group::getByID(GUEST_GROUP_ID),
                'now' => new DateTime('now'),
                'ak_exclude_sitemapxml' => CollectionAttributeKey::getByHandle('exclude_sitemapxml'),
                'ak_sitemap_changefreq' => CollectionAttributeKey::getByHandle('sitemap_changefreq'),
                'ak_sitemap_priority' => CollectionAttributeKey::getByHandle('sitemap_priority'),
            );
            $instances['guestGroupAE'] = array(GroupPermissionAccessEntity::getOrCreate($instances['guestGroup']));
            if (\Core::make('multilingual/detector')->isEnabled()) {
                $instances['multilingualSections'] = MultilingualSection::getList();
            } else {
                $instances['multilingualSections'] = array();
            }
            $xml = '<?xml version="1.0" encoding="' . APP_CHARSET . '"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
            if ($instances['multilingualSections']) {
                $xml .= ' xmlns:x="http://www.w3.org/1999/xhtml"';
            }
            $xml .= ' />';
            $xmlDoc = new SimpleXMLElement($xml);
            $rs = Database::get()->query('SELECT cID FROM Pages');
            while ($row = $rs->FetchRow()) {
                self::addPage($xmlDoc, intval($row['cID']), $instances);
            }
            $rs->Close();

            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('xmlDoc', $xmlDoc);
            Events::dispatch('on_sitemap_xml_ready', $event);

            $dom = dom_import_simplexml($xmlDoc)->ownerDocument;
            $dom->formatOutput = true;
            $addedPages = count($xmlDoc->url);
            $relName = ltrim(Config::get('concrete.sitemap_xml.file'), '\\/');
            $osName = rtrim(DIR_BASE, '\\/') . '/' . $relName;
            $urlName = rtrim(\Core::getApplicationURL(), '\\/') . '/' . $relName;
            if (!file_exists($osName)) {
                @touch($osName);
            }
            if (!is_writable($osName)) {
                throw new \Exception(t('The file %s is not writable', $osName));
            }
            if (!$hFile = @fopen($osName, 'w')) {
                throw new \Exception(t('Cannot open file %s', $osName));
            }
            if (!@fwrite($hFile, $dom->saveXML())) {
                throw new \Exception(t('Error writing to file %s', $osName));
            }
            @fflush($hFile);
            @fclose($hFile);
            unset($hFile);

            return t(
                '%1$s file saved (%2$d pages).',
                sprintf('<a href="%s" target="_blank">%s</a>', $urlName, preg_replace('/^https?:\/\//i', '', $urlName)),
                $addedPages
            );
        } catch (\Exception $x) {
            if (isset($hFile) && $hFile) {
                @fflush($hFile);
                @ftruncate($hFile, 0);
                @fclose($hFile);
                $hFile = null;
            }
            throw $x;
        }
    }

    /** Check if the specified page should be included in the sitemap.xml file; if so adds it to the XML document.
     * @param SimpleXMLElement $xmlDoc The xml document containing the sitemap nodes.
     * @param int $cID The page collection id.
     * @param array $instances An array with some already instantiated helpers, models, ...
     *
     * @throws \Exception Throws an exception in case of errors.
     */
    private static function addPage($xmlDoc, $cID, $instances)
    {
        $page = Page::getByID($cID, 'ACTIVE');
        if (!static::canIncludePageInSitemap($page, $instances)) {
            return;
        }
        $lastmod = new DateTime($page->getCollectionDateLastModified());
        $changefreq = $page->getAttribute($instances['ak_sitemap_changefreq']);
        $priority = $page->getAttribute($instances['ak_sitemap_priority']);
        $xmlNode = $xmlDoc->addChild('url');
        $xmlNode->addChild(
            'loc',
            $instances['navigation']->getLinkToCollection($page)
        );
        $xmlNode->addChild('lastmod', $lastmod->format(DateTime::ATOM));
        $xmlNode->addChild(
            'changefreq',
            Core::make('helper/validation/strings')->notempty($changefreq) ? $changefreq : Config::get('concrete.sitemap_xml.frequency')
        );
        $xmlNode->addChild(
            'priority',
            is_numeric($priority) ? $priority : Config::get('concrete.sitemap_xml.priority')
        );
        if ($instances['multilingualSections']) {
            $thisSection = MultilingualSection::getBySectionOfSite($page);
            if (is_object($thisSection) && !$thisSection->isError()) {
                foreach ($instances['multilingualSections'] as $section) {
                    $relatedPageID = $section->getTranslatedPageID($page);
                    if ($relatedPageID) {
                        $relatedPage = Page::getByID($relatedPageID);
                        if (static::canIncludePageInSitemap($relatedPage, $instances)) {
                            $xmlAltNode = $xmlNode->addChild('link', null, 'http://www.w3.org/1999/xhtml');
                            $xmlAltNode->addAttribute('rel', 'alternate');
                            $xmlAltNode->addAttribute('hreflang', strtolower(str_replace('_', '-', $section->getLocale())));
                            $xmlAltNode->addAttribute('href', $instances['navigation']->getLinkToCollection($relatedPage));
                        }
                    }
                }
            }
        }

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('xmlNode', $xmlNode);
        $event->setArgument('page', $page);
        Events::dispatch('on_sitemap_xml_addingpage', $event);

        if ((!empty($ret)) && ($ret < 0)) {
            for ($i = count($xmlDoc->url) - 1; $i >= 0; --$i) {
                if ($xmlDoc->url[$i] == $xmlNode) {
                    unset($xmlDoc->url[$i]);
                    break;
                }
            }
        }
    }

    protected static function canIncludePageInSitemap($page, $instances)
    {
        if (!is_object($page) || $page->isError()) {
            return false;
        }
        /* @var $page Page */
        if ($page->isSystemPage()) {
            return false;
        }
        if ($page->isExternalLink()) {
            return false;
        }
        if ($instances['dashboard']->inDashboard($page)) {
            return false;
        }
        if ($page->isInTrash()) {
            return false;
        }
        $pageVersion = $page->getVersionObject();
        if ($pageVersion && !$pageVersion->isApproved()) {
            return false;
        }
        $pubDate = new DateTime($page->getCollectionDatePublic());
        if ($pubDate > $instances['now']) {
            return false;
        }
        if ($page->getAttribute($instances['ak_exclude_sitemapxml'])) {
            return false;
        }
        $instances['view_page']->setPermissionObject($page);
        $pa = $instances['view_page']->getPermissionAccessObject();
        if (!is_object($pa)) {
            return false;
        }
        if (!$pa->validateAccessEntities($instances['guestGroupAE'])) {
            return false;
        }

        return true;
    }
}
