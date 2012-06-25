<?

/**
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A way to export concrete5 content as an xml representation.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Content_Exporter {
	
	protected $x; // the xml object for export
	protected static $mcBlockIDs = array();
	
	public function run() {
		$this->x = new SimpleXMLElement("<concrete5-cif></concrete5-cif>");
		$this->x->addAttribute('version', '1.0');

		// First, attribute categories
		AttributeKeyCategory::exportList($this->x);

		// attribute types
		AttributeType::exportList($this->x);

		// then block types
		BlockTypeList::exportList($this->x);

		// now attribute keys (including user)
		AttributeKey::exportList($this->x);

		// now attribute keys (including user)
		AttributeSet::exportList($this->x);

		// now theme
		PageTheme::exportList($this->x);
		
		// now packages
		PackageList::export($this->x);

		// permission access entity types
		PermissionAccessEntityType::exportList($this->x);
		
		// now task permissions
		PermissionKey::exportList($this->x);

		// workflow types
		WorkflowType::exportList($this->x);
		
		// now jobs
		Loader::model('job');
		Job::exportList($this->x);
		
		// now single pages
		$singlepages = $this->x->addChild("singlepages");
		$db = Loader::db();
		$r = $db->Execute('select cID from Pages where cFilename is not null and cFilename <> "" and cID not in (select cID from Stacks) order by cID asc');
		while($row = $r->FetchRow()) {
			$pc = Page::getByID($row['cID'], 'RECENT');
			$pc->export($singlepages);
		}		
		
		// now page types
		CollectionType::exportList($this->x);
		
		// now stacks/global areas
		Loader::model('stack/list');
		StackList::export($this->x);
		
		// now content pages
		$pages = $this->x->addChild("pages");
		$db = Loader::db();
		$r = $db->Execute('select Pages.cID from Pages left join ComposerDrafts on Pages.cID = ComposerDrafts.cID where ComposerDrafts.cID is null and cIsTemplate = 0 and cFilename is null or cFilename = "" order by cID asc');
		while($row = $r->FetchRow()) {
			$pc = Page::getByID($row['cID'], 'RECENT');
			$pc->export($pages);
		}		
		
		Loader::model("system/captcha/library");		
		SystemCaptchaLibrary::exportList($this->x);
		
		Config::exportList($this->x);
		
	}
	
	public static function addMasterCollectionBlockID($b, $id) {
		self::$mcBlockIDs[$b->getBlockID()] = $id;
	}
	
	public static function getMasterCollectionTemporaryBlockID($b) {
		if (isset(self::$mcBlockIDs[$b->getBlockID()])) {
			return self::$mcBlockIDs[$b->getBlockID()];
		}
	}
	
	public function output() {
		return $this->x->asXML();
		
	}
	
	public function getFilesArchive() {
		Loader::model('file_list');
		$vh = Loader::helper("validation/identifier");
		$archive = $vh->getString();
		FileList::exportArchive($archive);
		return $archive;
	}
	
	public static function replacePageWithPlaceHolder($cID) {
		if ($cID > 0) { 
			$c = Page::getByID($cID);
			return '{ccm:export:page:' . $c->getCollectionPath() . '}';
		}
	}

	public static function replaceFileWithPlaceHolder($fID) {
		if ($fID > 0) { 
			$f = File::getByID($fID);
			return '{ccm:export:file:' . $f->getFileName() . '}';
		}
	}

	public static function replacePageWithPlaceHolderInMatch($cID) {
		if ($cID[1] > 0) { 
			$cID = $cID[1];
			return self::replacePageWithPlaceHolder($cID);
		}
	}

	public static function replaceFileWithPlaceHolderInMatch($fID) {
		if ($fID[1] > 0) { 
			$fID = $fID[1];
			return self::replaceFileWithPlaceHolder($fID);
		}
	}
	
	public static function replaceImageWithPlaceHolderInMatch($fID) {
		if ($fID > 0) { 
			$f = File::getByID($fID[1]);
			return '{ccm:export:image:' . $f->getFileName() . '}';
		}
	}

	public static function replacePageTypeWithPlaceHolder($ctID) {
		if ($ctID > 0) {
			$ct = CollectionType::getByID($ctID);
			return '{ccm:export:pagetype:' . $ct->getCollectionTypeHandle() . '}';
		}
	}
	
	/** 
	 * Removes an item from the export xml registry
	 */
	public function removeItem($parent, $node, $handle) {
		$query = '//'.$node.'[@handle=\''.$handle.'\' or @package=\''.$handle.'\']';
		$r = $this->x->xpath($query);
		if ($r && isset($r[0]) && $r[0] instanceof SimpleXMLElement) {		
			$dom = dom_import_simplexml($r[0]);
			$dom->parentNode->removeChild($dom);
		}

		$query = '//'.$parent;
		$r = $this->x->xpath($query);
		if ($r && isset($r[0]) && $r[0] instanceof SimpleXMLElement) {		
			$dom = dom_import_simplexml($r[0]);
			if ($dom->childNodes->length < 1) {
				$dom->parentNode->removeChild($dom);
			}
		}
	}


}