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




}