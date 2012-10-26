<?

/**
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A way to import concrete5 content.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Content_Importer {
	
	protected static $mcBlockIDs = array();
	
	public function importContentFile($file) {
		$sx = simplexml_load_file($file);
		$this->importSinglePageStructure($sx);
		$this->importStacksStructure($sx);
		$this->importBlockTypes($sx);
		$this->importAttributeCategories($sx);
		$this->importAttributeTypes($sx);
		$this->importWorkflowTypes($sx);
		$this->importWorkflowProgressCategories($sx);
		$this->importAttributes($sx);
		$this->importAttributeSets($sx);
		$this->importThemes($sx);
		$this->importPermissionCategories($sx);
		$this->importPermissionAccessEntityTypes($sx);
		$this->importTaskPermissions($sx);
		$this->importPermissions($sx);
		$this->importJobs($sx);
		$this->importJobSets($sx);
		// import bare page types first, then import structure, then page types blocks, attributes and composer settings, then page content, because we need the structure for certain attributes and stuff set in master collections (like composer)
		$this->importPageTypesBase($sx);
		$this->importPageStructure($sx);
		$this->importPageTypeDefaults($sx);
		$this->importSinglePageContent($sx);
		$this->importStacksContent($sx);
		$this->importPageContent($sx);
		$this->importPackages($sx);
		$this->importConfigValues($sx);
		$this->importSystemCaptchaLibraries($sx);
	}
	
	protected static function getPackageObject($pkgHandle) {
		$pkg = false;
		if ($pkgHandle) {
			$pkg = Package::getByHandle($pkgHandle);
		}
		return $pkg;		
	}

	protected function importStacksStructure(SimpleXMLElement $sx) {
		if (isset($sx->stacks)) {
			foreach($sx->stacks->stack as $p) {
				if (isset($p['type'])) {
					$type = Stack::mapImportTextToType($p['type']);
					Stack::addStack($p['name'], $type);
				} else {
					Stack::addStack($p['name']);
				}
			}
		}
	}

	protected function importStacksContent(SimpleXMLElement $sx) {
		if (isset($sx->stacks)) {
			foreach($sx->stacks->stack as $p) {
				$stack = Stack::getByName($p['name']);
				if (isset($p->area)) {
					$this->importPageAreas($stack, $p);
				}
			}
		}
	}
	
	protected function importSinglePageStructure(SimpleXMLElement $sx) {
		if (isset($sx->singlepages)) {
			foreach($sx->singlepages->page as $p) {
				$pkg = ContentImporter::getPackageObject($p['package']);
				$spl = SinglePage::add($p['path'], $pkg);
				if (is_object($spl)) { 
					if (isset($p['root']) && $p['root'] == true) {
						$spl->moveToRoot();
					}
					if ($p['name']) {
						$spl->update(array('cName' => $p['name'], 'cDescription' => $p['description']));
					}
				}
			}
		}
	}

	protected function importSinglePageContent(SimpleXMLElement $sx) {
		if (isset($sx->singlepages)) {
			foreach($sx->singlepages->page as $px) {
				$page = Page::getByPath($px['path'], 'RECENT');
				if (isset($px->area)) {
					$this->importPageAreas($page, $px);
				}
				if (isset($px->attributes)) {
					foreach($px->attributes->children() as $attr) {
						$ak = CollectionAttributeKey::getByHandle($attr['handle']);
						if (is_object($ak)) { 
							$page->setAttribute((string) $attr['handle'], $ak->getController()->importValue($attr));
						}
					}
				}
			}
		}
	}

	protected function setupPageNodeOrder($pageNodeA, $pageNodeB) {
		$pathA = (string) $pageNodeA['path'];
		$pathB = (string) $pageNodeB['path'];
		$numA = count(explode('/', $pathA));
		$numB = count(explode('/', $pathB));
		if ($numA == $numB) {
			if (intval($pageNodeA->originalPos) < intval($pageNodeB->originalPos)) {
				return -1;
			} else if (intval($pageNodeA->originalPos) > intval($pageNodeB->originalPos)) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return ($numA < $numB) ? -1 : 1;
		}
	}
	
	protected function importPageContent(SimpleXMLElement $sx) {
		if (isset($sx->pages)) {
			foreach($sx->pages->page as $px) {
				if ($px['path'] != '') {
					$page = Page::getByPath($px['path'], 'RECENT');
				} else {
					$page = Page::getByID(HOME_CID, 'RECENT');
				}
				if (isset($px->area)) {
					$this->importPageAreas($page, $px);
				}
				if (isset($px->attributes)) {
					foreach($px->attributes->children() as $attr) {
						$ak = CollectionAttributeKey::getByHandle($attr['handle']);
						if (is_object($ak)) { 
							$page->setAttribute((string) $attr['handle'], $ak->getController()->importValue($attr));
						}
					}
				}
				$page->reindex();
			}
		}
	}
	
	protected function importPageStructure(SimpleXMLElement $sx) {
		if (isset($sx->pages)) {
			$nodes = array();
			$i = 0;
			foreach($sx->pages->page as $p) {
				$p->originalPos = $i;
				$nodes[] = $p;
				$i++;
			}
			usort($nodes, array('ContentImporter', 'setupPageNodeOrder'));
			$home = Page::getByID(HOME_CID, 'RECENT');

			foreach($nodes as $px) {
				$pkg = ContentImporter::getPackageObject($px['package']);
				$data = array();
				$data['pkgID'] = 0;
				if (is_object($pkg)) {
					$data['pkgID'] = $pkg->getPackageID();
				}
				$args = array();
				$ct = CollectionType::getByHandle($px['pagetype']);
				if ($px['path'] == '') {
					// home page
					$page = $home;
					$args['ctID'] = $ct->getCollectionTypeID();
				} else {
					$page = Page::getByPath($px['path']);
					if (!is_object($page) || ($page->isError())) {
						$lastSlash = strrpos((string) $px['path'], '/');
						$parentPath = substr((string) $px['path'], 0, $lastSlash);
						$data['cHandle'] = substr((string) $px['path'], $lastSlash + 1);
						if (!$parentPath) {
							$parent = $home;
						} else {
							$parent = Page::getByPath($parentPath);
						}
						$page = $parent->add($ct, $data);

					}
				}
				$args['cName'] = $px['name'];
				$args['cDescription'] = $px['description'];
				$args['ctID'] = $ct->getCollectionTypeID();
				$page->update($args);
			}
		}
	}
	
	protected function importPageAreas(Page $page, SimpleXMLElement $px) {
		foreach($px->area as $ax) {
			if (isset($ax->block)) {
				foreach($ax->block as $bx) {
					if ($bx['type'] != '') {
						// we check this because you might just get a block node with only an mc-block-id, if it's an alias
						$bt = BlockType::getByHandle($bx['type']);
						$btc = $bt->getController();
						$btc->import($page, (string) $ax['name'], $bx);
					} else if ($bx['mc-block-id'] != '') {
					
						// we find that block in the master collection block pool and alias it out
						$bID = array_search((string) $bx['mc-block-id'], self::$mcBlockIDs);
						if ($bID) {
							$mc = Page::getByID($page->getMasterCollectionID(), 'RECENT');
							$block = Block::getByID($bID, $mc, (string) $ax['name']);
							$block->alias($page);
						}
					}
				}
			}
		}
	}

	public static function addMasterCollectionBlockID($b, $id) {
		self::$mcBlockIDs[$b->getBlockID()] = $id;
	}
	
	public static function getMasterCollectionTemporaryBlockID($b) {
		if (isset(self::$mcBlockIDs[$b->getBlockID()])) {
			return self::$mcBlockIDs[$b->getBlockID()];
		}
	}
	
	protected function importPageTypesBase(SimpleXMLElement $sx) {
		if (isset($sx->pagetypes)) {
			foreach($sx->pagetypes->pagetype as $ct) {
				$pkg = ContentImporter::getPackageObject($ct['package']);
				$ctt = CollectionType::getByHandle($ct['handle']);
				if (!is_object($ctt)) { 
					$ctr = CollectionType::add(array(
						'ctHandle' => $ct['handle'],
						'ctName' => $ct['name'],
						'ctIcon' => $ct['icon'],
						'ctIsInternal' => (string) $ct['internal']
					), $pkg);
				}
			}
		}
	}

	protected function importPageTypeDefaults(SimpleXMLElement $sx) {
		$db = Loader::db();
		if (isset($sx->pagetypes)) {
			foreach($sx->pagetypes->pagetype as $ct) {
				$ctr = CollectionType::getByHandle((string) $ct['handle']);
				$mc = Page::getByID($ctr->getMasterCollectionID(), 'RECENT');
				if (isset($ct->page)) {
					$this->importPageAreas($mc, $ct->page);
				}
				if (isset($ct->composer)) {
					$ctr = CollectionType::getByHandle((string) $ct['handle']);
					$ctr->importComposerSettings($ct->composer);
				}
			}
		}
	}

	protected function importBlockTypes(SimpleXMLElement $sx) {
		if (isset($sx->blocktypes)) {
			foreach($sx->blocktypes->blocktype as $bt) {
				$pkg = ContentImporter::getPackageObject($bt['package']);
				if (is_object($pkg)) {
					BlockType::installBlockTypeFromPackage($bt['handle'], $pkg);
				} else {
					BlockType::installBlockType($bt['handle']);				
				}
			}
		}
	}

	protected function importWorkflowTypes(SimpleXMLElement $sx) {
		if (isset($sx->workflowtypes)) {
			foreach($sx->workflowtypes->workflowtype as $wt) {
				$pkg = ContentImporter::getPackageObject($wt['package']);
				$name = $wt['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($wt['handle']);
				}
				$type = WorkflowType::add($wt['handle'], $name, $pkg);
			}
		}
	}

	protected function importAttributeTypes(SimpleXMLElement $sx) {
		if (isset($sx->attributetypes)) {
			foreach($sx->attributetypes->attributetype as $at) {
				$pkg = ContentImporter::getPackageObject($at['package']);
				$name = $at['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($at['handle']);
				}
				$type = AttributeType::add($at['handle'], $name, $pkg);
				if (isset($at->categories)) {
					foreach($at->categories->children() as $cat) {
						$catobj = AttributeKeyCategory::getByHandle((string) $cat['handle']);
						$catobj->associateAttributeKeyType($type);
					}
				}
			}
		}
	}

	protected function importPermissionAccessEntityTypes(SimpleXMLElement $sx) {
		if (isset($sx->permissionaccessentitytypes)) {
			foreach($sx->permissionaccessentitytypes->permissionaccessentitytype as $pt) {
				$pkg = ContentImporter::getPackageObject($pt['package']);
				$name = $pt['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($pt['handle']);
				}
				$type = PermissionAccessEntityType::add($pt['handle'], $name, $pkg);
				if (isset($pt->categories)) {
					foreach($pt->categories->children() as $cat) {
						$catobj = PermissionKeyCategory::getByHandle((string) $cat['handle']);
						$catobj->associateAccessEntityType($type);
					}
				}
			}
		}
	}
	
	protected function importPackages(SimpleXMLElement $sx) {
		if (isset($sx->packages)) {
			foreach($sx->packages->package as $p) {
				$pkg = Loader::package((string) $p['handle']);
				$pkg->install();
			}
		}
	}
	
	protected function importThemes(SimpleXMLElement $sx) {
		if (isset($sx->themes)) {
			foreach($sx->themes->theme as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$ptHandle = (string) $th['handle'];
				$pt = PageTheme::getByHandle($ptHandle);
				if (!is_object($pt)) {
					$pt = PageTheme::add($ptHandle, $pkg);
				}
				if ($th['activated'] == '1') {
					$pt->applyToSite();
				}
			}
		}
	}

	protected function importSystemCaptchaLibraries(SimpleXMLElement $sx) {
		if (isset($sx->systemcaptcha)) {
			Loader::model('system/captcha/library');
			foreach($sx->systemcaptcha->library as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$scl = SystemCaptchaLibrary::add($th['handle'], $th['name'], $pkg);
				if ($th['activated'] == '1') {
					$scl->activate();
				}
			}
		}
	}

	protected function importJobs(SimpleXMLElement $sx) {
		Loader::model('job');
		if (isset($sx->jobs)) {
			foreach($sx->jobs->job as $jx) {
				$pkg = ContentImporter::getPackageObject($jx['package']);
				if (is_object($pkg)) {
					Job::installByPackage($jx['handle'], $pkg);
				} else {
					Job::installByHandle($jx['handle']);				
				}
			}
		}
	}

	protected function importJobSets(SimpleXMLElement $sx) {
		if (isset($sx->jobsets)) {
			foreach($sx->jobsets->jobset as $js) {
				$pkg = ContentImporter::getPackageObject($js['package']);
				$jso = JobSet::getByName((string) $js['name']);
				if (!is_object($jso)) {
					$jso = JobSet::add((string) $js['name']);
				}
				foreach($js->children() as $jsk) {
					$j = Job::getByHandle((string) $jsk['handle']);
					if (is_object($j)) { 	
						$jso->addJob($j);
					}
				}
			}
		}
	}

	protected function importConfigValues(SimpleXMLElement $sx) {
		if (isset($sx->config)) {
			$db = Loader::db();
			$configstore = new ConfigStore($db);
			foreach($sx->config->children() as $key) {
				$pkg = ContentImporter::getPackageObject($key['package']);
				if (is_object($pkg)) {
					$configstore->set($key->getName(), (string) $key, $pkg->getPackageID());
				} else {
					$configstore->set($key->getName(), (string) $key);
				}
			}
		}
	}

	protected function importTaskPermissions(SimpleXMLElement $sx) {
		if (isset($sx->taskpermissions)) {
			foreach($sx->taskpermissions->taskpermission as $tp) {
				$pkg = ContentImporter::getPackageObject($tp['package']);
				$tpa = TaskPermission::addTask($tp['handle'], $tp['name'], $tp['description'], $pkg);
				if (isset($tp->access)) {
					foreach($tp->access->children() as $ch) {
						if ($ch->getName() == 'group') {
							$g = Group::getByName($ch['name']);
							if (!is_object($g)) {
								$g = Group::add($ch['name'], $ch['description']);
							}
							$tpa->addAccess($g);
						}
					}
				}
			}
		}
	}

	protected function importPermissionCategories(SimpleXMLElement $sx) {
		if (isset($sx->permissioncategories)) {
			foreach($sx->permissioncategories->category as $pkc) {
				$pkg = ContentImporter::getPackageObject($akc['package']);
				$pkx = PermissionKeyCategory::add((string) $pkc['handle'], $pkg);
			}
		}
	}

	protected function importWorkflowProgressCategories(SimpleXMLElement $sx) {
		if (isset($sx->workflowprogresscategories)) {
			foreach($sx->workflowprogresscategories->category as $wpc) {
				$pkg = ContentImporter::getPackageObject($wpc['package']);
				$wkx = WorkflowProgressCategory::add((string) $wpc['handle'], $pkg);
			}
		}
	}

	protected function importPermissions(SimpleXMLElement $sx) {
		if (isset($sx->permissionkeys)) {
			foreach($sx->permissionkeys->permissionkey as $pk) {
				$pkc = PermissionKeyCategory::getByHandle((string) $pk['category']);
				$pkg = ContentImporter::getPackageObject($pk['package']);
				$txt = Loader::helper('text');
				$className = $txt->camelcase($pkc->getPermissionKeyCategoryHandle());
				$c1 = $className . 'PermissionKey';
				$pkx = call_user_func(array($c1, 'import'), $pk);	
				if (isset($pk->access)) {
					foreach($pk->access->children() as $ch) {
						if ($ch->getName() == 'group') {
							$g = Group::getByName($ch['name']);
							if (!is_object($g)) {
								$g = Group::add($g['name'], $g['description']);
							}
							$pae = GroupPermissionAccessEntity::getOrCreate($g);
							$pa = PermissionAccess::create($pkx);
							$pa->addListItem($pae);
							$pt = $pkx->getPermissionAssignmentObject();
							$pt->assignPermissionAccess($pa);
						}
					}
				}
			
			}
		}
	}

	protected function importAttributeCategories(SimpleXMLElement $sx) {
		if (isset($sx->attributecategories)) {
			foreach($sx->attributecategories->category as $akc) {
				$pkg = ContentImporter::getPackageObject($akc['package']);
				$akx = AttributeKeyCategory::add($akc['handle'], $akc['allow-sets'], $pkg);
			}
		}
	}
	
	protected function importAttributes(SimpleXMLElement $sx) {
		if (isset($sx->attributekeys)) {
			foreach($sx->attributekeys->attributekey as $ak) {
				$akc = AttributeKeyCategory::getByHandle($ak['category']);
				$pkg = ContentImporter::getPackageObject($ak['package']);
				$type = AttributeType::getByHandle($ak['type']);
				$txt = Loader::helper('text');
				$className = $txt->camelcase($akc->getAttributeKeyCategoryHandle());
				$c1 = $className . 'AttributeKey';
				$ak = call_user_func(array($c1, 'import'), $ak);				
			}
		}
	}

	protected function importAttributeSets(SimpleXMLElement $sx) {
		if (isset($sx->attributesets)) {
			foreach($sx->attributesets->attributeset as $as) {
				$akc = AttributeKeyCategory::getByHandle($as['category']);
				$pkg = ContentImporter::getPackageObject($as['package']);
				$set = $akc->addSet((string) $as['handle'], (string) $as['name'], $pkg, $as['locked']);
				foreach($as->children() as $ask) {
					$ak = $akc->getAttributeKeyByHandle((string) $ask['handle']);
					if (is_object($ak)) { 	
						$set->addKey($ak);
					}
				}
			}
		}
	}

	public static function getValue($value) {
		if (preg_match('/\{ccm:export:page:(.*)\}|\{ccm:export:file:(.*)\}|\{ccm:export:image:(.*)\}|\{ccm:export:pagetype:(.*)\}/i', $value, $matches)) {
			if ($matches[1]) {
				$c = Page::getByPath($matches[1]);
				return $c->getCollectionID();
			}
			if ($matches[2]) {
				$db = Loader::db();
				$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($matches[2]));
				return $fID;
			}
			if ($matches[3]) {
				$db = Loader::db();
				$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($matches[3]));
				return $fID;
			}
			if ($matches[4]) {
				$ct = CollectionType::getByHandle($matches[4]);
				return $ct->getCollectionTypeID();
			}
		} else {
			return $value;
		}
	}	

}