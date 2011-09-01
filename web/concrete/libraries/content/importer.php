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
class ContentImporter {
	
	public function importContentFile($file) {
		$sx = simplexml_load_file($file);
		$this->importSinglePages($sx);
		$this->importBlockTypes($sx);
		$this->importAttributeTypes($sx);
		$this->importAttributes($sx);
		$this->importThemes($sx);
		$this->importTaskPermissions($sx);
		$this->importJobs($sx);
		$this->importPageTypes($sx);
		$this->importPages($sx);
		
		// export attributes // import attributes
		// page types, pages, blocks, areas, etc...
	}
	
	protected static function getPackageObject($pkgHandle) {
		$pkg = false;
		if ($pkgHandle) {
			$pkg = Package::getByHandle($pkgHandle);
		}
		return $pkg;		
	}
	
	protected function importSinglePages(SimpleXMLElement $sx) {
		Loader::model('single_page');
		if (isset($sx->singlepages)) {
			foreach($sx->singlepages->page as $p) {
				$pkg = ContentImporter::getPackageObject($p['package']);
				$spl = SinglePage::add($p['path'], $pkg);
				if ($p['name']) {
					$spl->update(array('cName' => $p['name'], 'cDescription' => $p['description']));
				}
			}
		}
	}

	protected function setupPageNodeOrder($pageNodeA, $pageNodeB) {
		$pathA = $pageNodeA['path']->__toString();
		$pathB = $pageNodeB['path']->__toString();
		$numA = explode('/', $pathA);
		$numB = explode('/', $pathB);
		if ($numA == $numB) {
			return 0;
		} else {
			return ($numA < $numB) ? -1 : 1;
		}
	}
	
	protected function importPages(SimpleXMLElement $sx) {
		if (isset($sx->pages)) {
			$nodes = array();
			foreach($sx->pages->page as $p) {
				$nodes[] = $p;
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
				
				if ($px['path'] == '') {
					// home page
					$page = $home;
				} else {
					$page = Page::getByPath($px['path']);
					if (!is_object($page) || ($page->isError())) {
						$ct = CollectionType::getByHandle($px['pagetype']);
						$lastSlash = strrpos($px['path']->__toString(), '/');
						$parentPath = substr($px['path']->__toString(), 0, $lastSlash);
						$data['cHandle'] = substr($px['path']->__toString(), $lastSlash + 1);
						if (!$parentPath) {
							$parent = $home;
						} else {
							$parent = Page::getByPath($parentPath);
						}
						$page = $parent->add($ct, $data);

					}
				}
				
				$page->update(array('cName' => $px['name'], 'cDescription' => $px['description']));
				if (isset($px->area)) {
					$this->importPageAreas($page, $px);
				}
			}
		}
	}
	
	protected function importPageAreas(Page $page, SimpleXMLElement $px) {
		foreach($px->area as $ax) {
			if (isset($ax->block)) {
				foreach($ax->block as $bx) {
					$bt = BlockType::getByHandle($bx['type']);
					$btc = $bt->getController();
					$btc->import($page, $ax['name']->__toString(), $bx);
				}
			}
		}
	}
	
	protected function importPageTypes(SimpleXMLElement $sx) {
		if (isset($sx->pagetypes)) {
			foreach($sx->pagetypes->pagetype as $ct) {
				$pkg = ContentImporter::getPackageObject($ct['package']);
				$ctr = CollectionType::add(array(
					'ctHandle' => $ct['handle'],
					'ctName' => $ct['name']
				), $pkg);
				
				$mc = Page::getByID($ctr->getMasterCollectionID());
				if (isset($ct->page)) {
					$this->importPageAreas($mc, $ct->page);
				}
			}
			
			// we loop twice because when we have a composer node that deals with page types we may 
			// not have created the page type yet
			
			foreach($sx->pagetypes->pagetype as $ct) {
				if (isset($ct->composer)) {
					$ctr = CollectionType::getByHandle($ct['handle']->__toString());
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

	protected function importAttributeTypes(SimpleXMLElement $sx) {
		if (isset($sx->attributetypes)) {
			foreach($sx->attributetypes->attributetype as $at) {
				$pkg = ContentImporter::getPackageObject($at['package']);
				$name = $at['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($at['handle']);
				}
				AttributeType::add($at['handle'], $name, $pkg);
			}
		}
	}

	protected function importThemes(SimpleXMLElement $sx) {
		if (isset($sx->themes)) {
			foreach($sx->themes->theme as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$pt = PageTheme::add($th['handle'], $pkg);
				if ($th['activated'] == '1') {
					$pt->applyToSite();
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

	protected function importTaskPermissions(SimpleXMLElement $sx) {
		if (isset($sx->taskpermissions)) {
			foreach($sx->taskpermissions->taskpermission as $tp) {
				$pkg = ContentImporter::getPackageObject($at['package']);
				$tpa = TaskPermission::addTask($tp['handle'], $tp['name'], $tp['description'], $pkg);
				if (isset($tp->access)) {
					foreach($tp->access->children() as $ch) {
						if ($ch->getName() == 'group') {
							$g = Group::getByName($ch['name']);
							if (!is_object($g)) {
								$g = Group::add($g['name'], $g['description']);
							}
							$tpa->addAccess($g);
						}
					}
				}
			}
		}
	}

	protected function importAttributes(SimpleXMLElement $sx) {
		if (isset($sx->attributekeys)) {
			foreach($sx->attributekeys->attributekey as $ak) {
				$akc = AttributeKeyCategory::getByHandle($ak['category']);
				$pkg = ContentImporter::getPackageObject($ak['package']);
				if (!is_object($akc)) {
					$akc = AttributeKeyCategory::add($ak['category'], AttributeKeyCategory::ASET_ALLOW_SINGLE, $pkg);
				}
				$type = AttributeType::getByHandle($ak['type']);
				if (is_object($pkg)) {
					Loader::model('attribute/categories/' . $akc->getAttributeKeyCategoryHandle(), $pkg->getPackageHandle());
				} else {
					Loader::model('attribute/categories/' . $akc->getAttributeKeyCategoryHandle());
				}		
				$txt = Loader::helper('text');
				$className = $txt->camelcase($akc->getAttributeKeyCategoryHandle());
				$c1 = $className . 'AttributeKey';
				$ak = call_user_func(array($c1, 'import'), $ak);				
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