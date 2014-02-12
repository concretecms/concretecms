<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_GenerateSitemap extends Job {

	/** The end-of-line terminator.
	* @var string
	*/
	const EOL = "\n";

	/** Returns the job name.
	* @return string
	*/
	public function getJobName() {
		return t('Generate the sitemap.xml file');
	}

	/** Returns the job description.
	* @return string
	*/
	public function getJobDescription() {
		return t('Generate the sitemap.xml file that search engines use to crawl your site.');
	}

	/** Executes the job.
	* @return string Returns a string describing the job result in case of success.
	* @throws Exception Throws an exception in case of errors.
	*/
	public function run() {
		Cache::disableCache();
		Cache::disableLocalCache();
		try {
			$db = Loader::db();
			$instances = array(
				'navigation' => Loader::helper('navigation'),
				'dashboard' => Loader::helper('concrete/dashboard'),
				'view_page' => PermissionKey::getByHandle('view_page'),
				'guestGroup' => Group::getByID(GUEST_GROUP_ID),
				'now' => new DateTime('now'),
				'ak_exclude_sitemapxml' => CollectionAttributeKey::getByHandle('exclude_sitemapxml'),
				'ak_sitemap_changefreq' => CollectionAttributeKey::getByHandle('sitemap_changefreq'),
				'ak_sitemap_priority' => CollectionAttributeKey::getByHandle('sitemap_priority')
			);
			$instances['guestGroupAE'] = array(GroupPermissionAccessEntity::getOrCreate($instances['guestGroup']));
			$xmlDoc = new SimpleXMLElement('<'.'?xml version="1.0" encoding="' . APP_CHARSET . '"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');
			$rs = Loader::db()->Query('SELECT cID FROM Pages');
			while($row = $rs->FetchRow()) {
				self::addPage($xmlDoc, intval($row['cID']), $instances);
			}
			$rs->Close();
			Events::fire('on_sitemap_xml_ready', $xmlDoc);
			$dom = dom_import_simplexml($xmlDoc)->ownerDocument;
			$dom->formatOutput = true;
			$addedPages = count($xmlDoc->url);
			$relName = ltrim(SITEMAPXML_FILE, '\\/');
			$osName = rtrim(DIR_BASE, '\\/') . '/' . $relName;
			$urlName = rtrim(BASE_URL . DIR_REL, '\\/') . '/' . $relName;
			if(!file_exists($osName)) {
				@touch($osName);
			}
			if(!is_writable($osName)) {
				throw new Exception(t('The file %s is not writable', $osName));
			}
			if(!$hFile = @fopen($osName, 'w')) {
				throw new Exception(t('Cannot open file %s', $osName));
			}
			if(!@fprintf($hFile, $dom->saveXML())) {
				throw new Exception(t('Error writing to file %s', $osName));
			}
			@fflush($hFile);
			@fclose($hFile);
			unset($hFile);
			return t('%1$s file saved (%2$d pages).', sprintf('<a href="%s" target="_blank">%s</a>', $urlName, preg_replace('/^https?:\/\//i', '', $urlName)), $addedPages);
		}
		catch(Exception $x) {
			if(isset($hFile) && $hFile) {
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
	* @throws Exception Throws an exception in case of errors.
	*/
	private static function addPage($xmlDoc, $cID, $instances) {
		$page = Page::getByID($cID, 'ACTIVE');
		if($page->isSystemPage()) {
			return;
		}
		if($page->isExternalLink()) {
			return;
		}
		if($instances['dashboard']->inDashboard($page)) {
			return;
		}
		if($page->isInTrash()) {
			return;
		}
		$pageVersion = $page->getVersionObject();
		if($pageVersion && !$pageVersion->isApproved()) {
			return;
		}
		$pubDate = new DateTime($page->getCollectionDatePublic());
		if($pubDate > $instances['now']) {
			return;
		}
		if($page->getAttribute($instances['ak_exclude_sitemapxml'])) {
			return;
		}
		$instances['view_page']->setPermissionObject($page);
		$pa = $instances['view_page']->getPermissionAccessObject();
		if (!is_object($pa)) {
			return;
		}
		if (!$pa->validateAccessEntities($instances['guestGroupAE'])) {
			return;
		}
		$lastmod = new DateTime($page->getCollectionDateLastModified());
		$changefreq = $page->getAttribute($instances['ak_sitemap_changefreq']);
		$priority = $page->getAttribute($instances['ak_sitemap_priority']);
		$xmlNode = $xmlDoc->addChild('url');
		$xmlNode->addChild('loc', SITEMAPXML_BASE_URL . $instances['navigation']->getLinkToCollection($page));
		$xmlNode->addChild('lastmod', $lastmod->format(DateTime::ATOM));
		$xmlNode->addChild('changefreq', empty($changefreq) ? SITEMAPXML_DEFAULT_CHANGEFREQ : $changefreq);
		$xmlNode->addChild('priority', is_numeric($priority) ? $priority : SITEMAPXML_DEFAULT_PRIORITY);
		$ret = Events::fire('on_sitemap_xml_addingpage', $xmlNode, $page);
		if((!empty($ret)) && ($ret < 0)) {
			for($i = count($xmlDoc->url) - 1; $i >= 0; $i--) {
				if($xmlDoc->url[$i] == $xmlNode) {
					unset($xmlDoc->url[$i]);
					break;
				}
			}
		}
	}
}
