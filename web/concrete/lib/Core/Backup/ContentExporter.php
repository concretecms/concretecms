<?
namespace Concrete\Core\Backup;
class ContentExporter {
	
	protected $x; // the xml object for export
	protected static $mcBlockIDs = array();
	protected static $ptComposerOutputControlIDs = array();
	
	public function run() {
		$this->x = new SimpleXMLElement("<concrete5-cif></concrete5-cif>");
		$this->x->addAttribute('version', '1.0');

		// First, attribute categories
		AttributeKeyCategory::exportList($this->x);

		// Features
		Feature::exportList($this->x);
		FeatureCategory::exportList($this->x);

		ConversationEditor::exportList($this->x);

		ConversationRatingType::exportList($this->x);

		// composer
		PageTypePublishTargetType::exportList($this->x);
		PageTypeComposerControlType::exportList($this->x);
		PageType::exportList($this->x);

		// attribute types
		AttributeType::exportList($this->x);

		// then block types
		BlockTypeList::exportList($this->x);

		// now block type sets (including user)
		BlockTypeSet::exportList($this->x);

		// gathering
		GatheringDataSource::exportList($this->x);
		GatheringItemTemplate::exportList($this->x);

		// now attribute keys (including user)
		AttributeKey::exportList($this->x);

		// now attribute keys (including user)
		AttributeSet::exportList($this->x);

		PageTemplate::exportList($this->x);

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
		
		// now stacks/global areas
		Loader::model('stack/list');
		StackList::export($this->x);
		
		// now content pages
		$pages = $this->x->addChild("pages");
		$db = Loader::db();
		$r = $db->Execute('select Pages.cID from Pages where cIsTemplate = 0 and cFilename is null or cFilename = "" order by cID asc');
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

	public static function addPageTypeComposerOutputControlID(PageTypeComposerFormLayoutSetControl $control, $id) {
		self::$ptComposerOutputControlIDs[$control->getPageTypeComposerFormLayoutSetControlID()] = $id;
	}
	
	public static function getPageTypeComposerOutputControlTemporaryID(PageTypeComposerFormLayoutSetControl $control) {
		if (isset(self::$ptComposerOutputControlIDs[$control->getPageTypeComposerFormLayoutSetControlID()])) {
			return self::$ptComposerOutputControlIDs[$control->getPageTypeComposerFormLayoutSetControlID()];
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

	public static function replacePageTypeWithPlaceHolder($ptID) {
		if ($ptID > 0) {
			$ct = PageType::getByID($ptID);
			return '{ccm:export:pagetype:' . $ct->getPageTypeHandle() . '}';
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
