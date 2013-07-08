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
				'now' => new DateTime('now')
			);
			$instances['guestGroupAE'] = array(GroupPermissionAccessEntity::getOrCreate($instances['guestGroup']));
			$rsPages = $db->query('SELECT cID FROM Pages WHERE (cID > 1) ORDER BY cID');
			$relName = ltrim(SITEMAPXML_FILE, '\\/');
			$osName = rtrim(DIR_BASE, '\\/') . '/' . $relName;
			$urlName = rtrim(BASE_URL . DIR_REL, '\\/') . '/' . $relName;
			if(!file_exists($osName)) {
				@touch($osName);
			}
			if(!is_writable($osName)) {
				throw new Exception(t('The file %s is not writable', $osName));
			}
			if(!$hFile = fopen($osName, 'w')) {
				throw new Exception(t('Cannot open file %s', $osName));
			}
			if(!@fprintf($hFile, '<'.'?xml version="1.0" encoding="%s"?>' . self::EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', APP_CHARSET)) {
				throw new Exception(t('Error writing header of %s', $osName));
			}
			$addedPages = 0;
			if(self::AddPage($hFile, 1, $instances)) {
				$addedPages++;
			}
			while($rowPage = $rsPages->FetchRow()) {
				if(self::AddPage($hFile, intval($rowPage['cID']), $instances)) {
					$addedPages++;
				}
			}
			$rsPages->Close();
			unset($rsPages);
			if(!@fwrite($hFile, self::EOL . '</urlset>')) {
				throw new Exception(t('Error writing footer of %s', $osName));
			}
			@fflush($hFile);
			@fclose($hFile);
			unset($hFile);
			return t('%1$s file saved (%2$d pages).', $urlName, $addedPages);
		}
		catch(Exception $x) {
			if(isset($rsPages) && $rsPages) {
				$rsPages->Close();
				$rsPages = null;
			}
			if(isset($hFile) && $hFile) {
				@fflush($hFile);
				@ftruncate($hFile, 0);
				@fclose($hFile);
				$hFile = null;
			}
			throw $x;
		}
	}

	/** Check if the specified page should be included in the sitemap.xml file; if so adds it to the file.
	* @param unknown_type $hFile
	* @param int $cID The page collection id.
	* @param array $instances Already instantiated helpers, models, ...
	* @return bool Returns true if the page has been added, false otherwise.
	* @throws Exception Throws an exception in case of errors.
	*/
	private static function AddPage($hFile, $cID, $instances) {
		$page = Page::getByID($cID, 'ACTIVE');
		if($page->isSystemPage()) {
			return false;
		}
		if($page->isExternalLink()) {
			return false;
		}
		if($instances['dashboard']->inDashboard($page)) {
			return false;
		}
		if($page->isInTrash()) {
			return false;
		}
		$pageVersion = $page->getVersionObject();
		if($pageVersion && !$pageVersion->isApproved()) {
			return false;
		}
		$pubDate = new DateTime($page->getCollectionDatePublic());
		if($pubDate > $instances['now']) {
			return false;
		}
		if($page->getAttribute('exclude_sitemapxml')) {
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
		$lastmod = new DateTime($page->getCollectionDateLastModified());
		$changefreq = $page->getAttribute('sitemap_changefreq');
		$priority = $page->getAttribute('sitemap_priority');
		$url = SITEMAPXML_BASE_URL . $instances['navigation']->getLinkToCollection($page);
		if(!@fprintf(
			$hFile,
			"%1\$s\t<url>%1\$s\t\t<loc>%2\$s</loc>%1\$s\t\t<lastmod>%3\$s</lastmod>%1\$s\t\t<changefreq>%4\$s</changefreq>%1\$s\t\t<priority>%5\$s</priority>%1\$s\t</url>",
			self::EOL,
			$url,
			$lastmod->format(DateTime::ATOM),
			htmlspecialchars(($changefreq == '') ? SITEMAPXML_DEFAULT_CHANGEFREQ : $changefreq),
			htmlspecialchars(($priority == '') ? SITEMAPXML_DEFAULT_PRIORITY : $priority)
		)) {
			throw new Exception(t('Error writing page with cID %d to sitemap.xml', $cID));
		}
		@fflush($hFile);
		return true;
	}
}
