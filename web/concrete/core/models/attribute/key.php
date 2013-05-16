<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AttributeKey extends Object {
	
	public function getIndexedSearchTable() {return false;}
	public function getSearchIndexFieldDefinition() {
		return $this->searchIndexFieldDefinition;
	}
	/** 
	 * Returns the name for this attribute key
	 */
	public function getAttributeKeyName() { return $this->akName;}

	/** 
	 * Returns the handle for this attribute key
	 */
	public function getAttributeKeyHandle() { return $this->akHandle;}
	
	/** 
	 * Deprecated. Going to be replaced by front end display name
	 */
	public function getAttributeKeyDisplayHandle() {
		return Loader::helper('text')->unhandle($this->akHandle);
	}
	
	/** 
	 * Returns the ID for this attribute key
	 */
	public function getAttributeKeyID() {return $this->akID;}
	public function getAttributeKeyCategoryID() {return $this->akCategoryID;}
	
	/** 
	 * Returns whether the attribute key is searchable 
	 */
	public function isAttributeKeySearchable() {return $this->akIsSearchable;}

	/** 
	 * Returns whether the attribute key is internal 
	 */
	public function isAttributeKeyInternal() {return $this->akIsInternal;}

	/** 
	 * Returns whether the attribute key is indexed as a "keyword search" field. 
	 */
	public function isAttributeKeyContentIndexed() {return $this->akIsSearchableIndexed;}

	/** 
	 * Returns whether the attribute key is one that was automatically created by a process. 
	 */
	public function isAttributeKeyAutoCreated() {return $this->akIsAutoCreated;}

	/** 
	 * Returns whether the attribute key is included in the standard search for this category. 
	 */
	public function isAttributeKeyColumnHeader() {return $this->akIsColumnHeader;}

	/** 
	 * Returns whether the attribute key is one that can be edited through the frontend. 
	 */
	public function isAttributeKeyEditable() {return $this->akIsEditable;}
	
	/** 
	 * Loads the required attribute fields for this instantiated attribute
	 */
	protected function load($akIdentifier, $loadBy = 'akID') {
		$db = Loader::db();
		$akunhandle = Loader::helper('text')->uncamelcase(get_class($this));
		$akCategoryHandle = substr($akunhandle, 0, strpos($akunhandle, '_attribute_key'));
		if ($akCategoryHandle != '') {
			$row = $db->GetRow('select akID, akHandle, akName, AttributeKeys.akCategoryID, akIsInternal, akIsEditable, akIsSearchable, akIsSearchableIndexed, akIsAutoCreated, akIsColumnHeader, AttributeKeys.atID, atHandle, AttributeKeys.pkgID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID inner join AttributeTypes on AttributeKeys.atID = AttributeTypes.atID where ' . $loadBy . ' = ? and akCategoryHandle = ?', array($akIdentifier, $akCategoryHandle));
		} else {
			$row = $db->GetRow('select akID, akHandle, akName, akCategoryID, akIsEditable, akIsInternal, akIsSearchable, akIsSearchableIndexed, akIsAutoCreated, akIsColumnHeader, AttributeKeys.atID, atHandle, AttributeKeys.pkgID from AttributeKeys inner join AttributeTypes on AttributeKeys.atID = AttributeTypes.atID where ' . $loadBy . ' = ?', array($akIdentifier));		
		}
		$this->setPropertiesFromArray($row);
	}
	
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public static function getInstanceByID($akID) {
		$db = Loader::db();
		$akCategoryID = $db->GetOne('select akCategoryID from AttributeKeys where akID = ?', $akID);
		if ($akCategoryID > 0) {
			$akc = AttributeKeyCategory::getByID($akCategoryID);
			return $akc->getAttributeKeyByID($akID);
		}
	}
	
	/** 
	 * Returns an attribute type object 
	 */
	public function getAttributeType() {
		return AttributeType::getByID($this->atID);
	}
	//deprecated
	public function getAttributeKeyType(){ return $this->getAttributeType(); }	
	
	/** 
	 * Returns a list of all attributes of this category
	 */
	public static function getList($akCategoryHandle, $filters = array()) {
		$db = Loader::db();
		$pkgHandle = $db->GetOne('select pkgHandle from AttributeKeyCategories inner join Packages on Packages.pkgID = AttributeKeyCategories.pkgID where akCategoryHandle = ?', array($akCategoryHandle));
		$q = 'select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akCategoryHandle = ?';
		foreach($filters as $key => $value) {
			$q .= ' and ' . $key . ' = ' . $value . ' ';
		}
		$r = $db->Execute($q, array($akCategoryHandle));
		$list = array();
		$txt = Loader::helper('text');
		if ($pkgHandle) { 
			Loader::model('attribute/categories/' . $akCategoryHandle, $pkgHandle);
		} else {
			Loader::model('attribute/categories/' . $akCategoryHandle);
		}
		$className = $txt->camelcase($akCategoryHandle);
		while ($row = $r->FetchRow()) {
			$c1 = $className . 'AttributeKey';
			$c1a = call_user_func(array($c1, 'getByID'), $row['akID']);
			if (is_object($c1a)) {
				$list[] = $c1a;
			}
		}
		$r->Close();
		return $list;
	}
	
	public function export($axml, $exporttype = 'full') {
		$type = $this->getAttributeType()->getAttributeTypeHandle();
		$category = AttributeKeyCategory::getByID($this->akCategoryID)->getAttributeKeyCategoryHandle();
		$akey = $axml->addChild('attributekey');
		$akey->addAttribute('handle',$this->getAttributeKeyHandle());
		
		if ($exporttype == 'full') { 
			$akey->addAttribute('name', $this->getAttributeKeyName());
			$akey->addAttribute('package', $this->getPackageHandle());
			$akey->addAttribute('searchable', $this->isAttributeKeySearchable());
			$akey->addAttribute('indexed', $this->isAttributeKeySearchable());
			$akey->addAttribute('type', $type);
			$akey->addAttribute('category', $category);
			$this->getController()->exportKey($akey);
		}
		
		return $akey;
	}

	public static function exportList($xml) {
		$categories = AttributeKeyCategory::getList();
		$axml = $xml->addChild('attributekeys');
		foreach($categories as $cat) {
			$attributes = AttributeKey::getList($cat->getAttributeKeyCategoryHandle());
			foreach($attributes as $at) {
				$at->export($axml);
			}
		}
	}
	
	/** 
	 * Note, this queries both the pkgID found on the AttributeKeys table AND any attribute keys of a special type
	 * installed by that package, and any in categories by that package.
	 * That's because a special type, if the package is uninstalled, is going to be unusable
	 * by attribute keys that still remain.
	 */
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$tina[] = '-1';
		$tinb = $db->GetCol('select atID from AttributeTypes where pkgID = ?', $pkg->getPackageID());
		if (is_array($tinb)) {
			$tina = array_merge($tina, $tinb);
		}
		$tinstr = implode(',', $tina);

		$kina[] = '-1';
		$kinb = $db->GetCol('select akCategoryID from AttributeKeyCategories where pkgID = ?', $pkg->getPackageID());
		if (is_array($kinb)) {
			$kina = array_merge($kina, $kinb);
		}
		$kinstr = implode(',', $kina);


		$r = $db->Execute('select akID, akCategoryID from AttributeKeys where (pkgID = ? or atID in (' . $tinstr . ') or akCategoryID in (' . $kinstr . ')) order by akID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$akc = AttributeKeyCategory::getByID($row['akCategoryID']);
			$ak = $akc->getAttributeKeyByID($row['akID']);
			$list[] = $ak;
		}
		$r->Close();
		return $list;
	}	
	
	public static function import(SimpleXMLElement $ak) {
		$type = AttributeType::getByHandle($ak['type']);
		$akCategoryHandle = $ak['category'];
		$pkg = false;
		if ($ak['package']) {
			$pkg = Package::getByHandle($ak['package']);
		}
		$akIsInternal = 0;
		if ($ak['internal']) {
			$akIsInternal = 1;
		}
		$akn = self::add($akCategoryHandle, $type, array('akHandle' => $ak['handle'], 'akName' => $ak['name'], 'akIsInternal' => $akIsInternal, 'akIsSearchableIndexed' => $ak['indexed'], 'akIsSearchable' => $ak['searchable']), $pkg);
		$akn->getController()->importKey($ak);
	}
	
	/** 
	 * Adds an attribute key. 
	 */
	protected function add($akCategoryHandle, $type, $args, $pkg = false) {
		
		$vn = Loader::helper('validation/numbers');
		$txt = Loader::helper('text');
		if (!is_object($type)) {
			// The passed item is not an object. It is probably something like 'DATE'
			$type = AttributeType::getByHandle(strtolower($type));
		}
		$atID = $type->getAttributeTypeID();

		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		
		extract($args);
		
		$_akIsSearchable = 1;
		$_akIsSearchableIndexed = 1;
		$_akIsAutoCreated = 1;
		$_akIsEditable = 1;
		$_akIsInternal = 0;
		
		if (!$akIsSearchable) {
			$_akIsSearchable = 0;
		}
		if ($akIsInternal) {
			$_akIsInternal = 1;
		}
		if (!$akIsSearchableIndexed) {
			$_akIsSearchableIndexed = 0;
		}
		if (!$akIsAutoCreated) {
			$_akIsAutoCreated = 0;
		}
		if (isset($akIsEditable) && (!$akIsEditable)) {
			$_akIsEditable = 0;
		}
		
		$db = Loader::db();
		$akCategoryID = $db->GetOne("select akCategoryID from AttributeKeyCategories where akCategoryHandle = ?", $akCategoryHandle);
		$a = array($akHandle, $akName, $_akIsSearchable, $_akIsSearchableIndexed, $_akIsInternal, $_akIsAutoCreated, $_akIsEditable, $atID, $akCategoryID, $pkgID);
		$r = $db->query("insert into AttributeKeys (akHandle, akName, akIsSearchable, akIsSearchableIndexed, akIsInternal, akIsAutoCreated, akIsEditable, atID, akCategoryID, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $a);
		
		$category = AttributeKeyCategory::getByID($akCategoryID);
		
		if ($r) {
			$akID = $db->Insert_ID();
			$className = $txt->camelcase($akCategoryHandle) . 'AttributeKey';
			$ak = new $className();
			$ak->load($akID);
			switch($category->allowAttributeSets()) {
				case AttributeKeyCategory::ASET_ALLOW_SINGLE:
					if ($asID > 0) {
						$ak->setAttributeSet(AttributeSet::getByID($asID));			
					}
					break;
			}

			$at = $ak->getAttributeType();
			$cnt = $at->getController();
			$cnt->setAttributeKey($ak);
			$cnt->saveKey($args);
			$ak->updateSearchIndex();
			
			$at->__destruct();
			unset($at);
			unset($cnt);		
			return $ak;
		}
	}

	public function refreshCache() {

	}
	
	/** 
	 * Updates an attribute key. 
	 */
	public function update($args) {
		$prevHandle = $this->getAttributeKeyHandle();

		extract($args);

		if (!$akIsSearchable) {
			$akIsSearchable = 0;
		}
		if (!$akIsSearchableIndexed) {
			$akIsSearchableIndexed = 0;
		}
		$db = Loader::db();

		$akCategoryHandle = $db->GetOne("select akCategoryHandle from AttributeKeyCategories inner join AttributeKeys on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akID = ?", $this->getAttributeKeyID());
		$a = array($akHandle, $akName, $akIsSearchable, $akIsSearchableIndexed, $this->getAttributeKeyID());
		$r = $db->query("update AttributeKeys set akHandle = ?, akName = ?, akIsSearchable = ?, akIsSearchableIndexed = ? where akID = ?", $a);
		
		$category = AttributeKeyCategory::getByID($this->akCategoryID);
		switch($category->allowAttributeSets()) {
			case AttributeKeyCategory::ASET_ALLOW_SINGLE:
				if ($asID > 0) {
					$as = AttributeSet::getByID($asID);
					if ((!$this->inAttributeSet($as)) && is_object($as)) {
						$this->clearAttributeSets();
						$this->setAttributeSet($as);
					}
				} else {
					// clear set
					$this->clearAttributeSets();
				}
				break;
		}

		
		if ($r) {
			$txt = Loader::helper('text');
			$className = $txt->camelcase($akCategoryHandle) . 'AttributeKey';
			$ak = new $className();
			$ak->load($this->getAttributeKeyID());
			$at = $ak->getAttributeType();
			$cnt = $at->getController();
			$cnt->setAttributeKey($ak);
			$cnt->saveKey($args);
			$ak->updateSearchIndex($prevHandle);
			return $ak;
		}
	}
	
	/** 
	 * Duplicates an attribute key 
	 */
	public function duplicate($args = array()) {
		$ar = new ADODB_Active_Record('AttributeKeys');
		$ar->Load('akID=?', array($this->akID));
		
		$ar2 = clone $ar;
		$ar2->akID = null;
		foreach($args as $key=>$value) {
			$ar2->{$key} = $value;
		}
		$ar2->Insert();
		$db = Loader::db();
		$ak = new AttributeKey();
		$ak->load($db->Insert_ID());
		
		// now we duplicate the specific category fields
		$this->getController()->duplicateKey($ak);
		
		return $ak;
	}
	
	public function inAttributeSet($as) {
		if (is_object($as)) {
			return $as->contains($this);
		}
	}
	
	public function setAttributeKeyColumnHeader($r) {
		$db = Loader::db();
		$r = ($r == true) ? 1 : 0;
		$db->Execute('update AttributeKeys set akIsColumnHeader = ? where akID = ?', array($r, $this->getAttributeKeyID()));
	}
	
	public function reindex($tbl, $columnHeaders, $attribs, $rs) {
		$db = Loader::db();
		$columns = $db->MetaColumns($tbl);
		
		foreach($attribs as $akHandle => $value) {
			if (is_array($value)) {
				foreach($value as $key => $v) {
					$column = 'ak_' . $akHandle . '_' . $key;
					if (isset($columns[strtoupper($column)])) {
						$columnHeaders[$column] = $v;
					}
				}
			} else {
				$column = 'ak_' . $akHandle;
				if (isset($columns[strtoupper($column)])) {
					$columnHeaders[$column] = $value;
				}
			}
		}
		
		//this shouldn't be necessary, but i had a saying telling me that the static variable 'db' was protected, 
		//even though it was declared as public 
		$q = $db->GetInsertSQL($rs, $columnHeaders);
		$r = $db->Execute($q);
		$r->Close();
		$rs->Close();
	}
	
	public function updateSearchIndex($prevHandle = false) {
		$type = $this->getAttributeType();
		$cnt = $type->getController();
		if ($this->getIndexedSearchTable() == false) {
			return false;
		}
		if ($cnt->getSearchIndexFieldDefinition() == false) {
			return false;
		}
		
		$fields = array();
		if (!is_array($cnt->getSearchIndexFieldDefinition())) {
			$fields[] = $this->akHandle . ' ' . $cnt->getSearchIndexFieldDefinition();
		} else {
			foreach($cnt->getSearchIndexFieldDefinition() as $col => $def) {
				$fields[$col] = $this->akHandle . '_' . $col . ' ' . $def;
			}
		}
		
		$db = Loader::db();
		$columns = $db->MetaColumns($this->getIndexedSearchTable());
		$dba = NewDataDictionary($db, DB_TYPE);
		
		foreach($fields as $col => $field) {

			$addColumn = true;

			$field = 'ak_' . $field;
			
			if (!is_int($col)) {
				$column = 'ak_' . $this->akHandle . '_' . $col;
			} else {
				$column = 'ak_' . $this->akHandle;
			}
			if ($prevHandle != false) {
				if (!is_int($col)) {
					$prevColumn = 'ak_' . $prevHandle . '_' . $col;
				} else {
					$prevColumn = 'ak_' . $prevHandle;
				}
			}
			
			if ($prevColumn != false) {
				if ($columns[strtoupper($prevColumn)]) {
					$q = $dba->RenameColumnSQL($this->getIndexedSearchTable(), $prevColumn, $column, $field);
					$db->Execute($q[0]);
					$addColumn = false;
				}
			}
			
			if ($addColumn) {
				if (!$columns[strtoupper($column)]) {
					$q = $dba->AddColumnSQL($this->getIndexedSearchTable(), $field);
					$db->Execute($q[0]);
				}
			}
			
		}
	}
	
	public function delete() {
		$at = $this->getAttributeType();
		$at->controller->setAttributeKey($this);
		$at->controller->deleteKey();
		$cnt = $this->getController();
		
		$db = Loader::db();
		$db->Execute('delete from AttributeKeys where akID = ?', array($this->getAttributeKeyID()));
		$db->Execute('delete from AttributeSetKeys where akID = ?', array($this->getAttributeKeyID()));

		if ($this->getIndexedSearchTable()) {
			$columns = $db->MetaColumns($this->getIndexedSearchTable());
			$dba = NewDataDictionary($db, DB_TYPE);
			
			$fields = array();
			if (!is_array($cnt->getSearchIndexFieldDefinition())) {
				$dropColumns[] = 'ak_' . $this->akHandle;
			} else {
				foreach($cnt->getSearchIndexFieldDefinition() as $col => $def) {
					$dropColumns[] = 'ak_' . $this->akHandle . '_' . $col;
				}
			}
			
			foreach($dropColumns as $dc) {
				if ($columns[strtoupper($dc)]) {
					$q = $dba->DropColumnSQL($this->getIndexedSearchTable(), $dc);
					$db->Execute($q[0]);
				}
			}
		}

	}
	
	public function getAttributeValueIDList() {
		$db = Loader::db();
		$ids = array();
		$r = $db->Execute('select avID from AttributeValues where akID = ?', array($this->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$ids[] = $row['avID'];
		}
		$r->Close();
		return $ids;
	}

	/** 
	 * Adds a generic attribute record (with this type) to the AttributeValues table
	 */
	public function addAttributeValue() {
		$db = Loader::db();
		$u = new User();
		$dh = Loader::helper('date');
		$uID = $u->isRegistered() ? $u->getUserID() : 0;
		$avDate = $dh->getLocalDateTime();
		$v = array($this->atID, $this->akID, $uID, $avDate);
		$db->Execute('insert into AttributeValues (atID, akID,  uID, avDateAdded) values (?, ?, ?, ?)', $v);
		$avID = $db->Insert_ID();
		return AttributeValue::getByID($avID);
	}
	
	public function getAttributeKeyIconSRC() {
		$type = $this->getAttributeType();
		return $type->getAttributeTypeIconSRC();
	}
	
	public function getController() {
		$at = AttributeType::getByHandle($this->atHandle);
		$cnt = $at->getController();
		$cnt->setAttributeKey($this);
		return $cnt;
	}
	
	/** 
	 * Renders a view for this attribute key. If no view is default we display it's "view"
	 * Valid views are "view", "form" or a custom view (if the attribute has one in its directory)
	 * Additionally, an attribute does not have to have its own interface. If it doesn't, then whatever
	 * is printed in the corresponding $view function in the attribute's controller is printed out.
	 */
	public function render($view = 'view', $value = false, $return = false) {
		$at = AttributeType::getByHandle($this->atHandle);
		$resp = $at->render($view, $this, $value, $return);
		if ($return) {
			return $resp;
		}
	}
	
	/** 
	 * Calls the functions necessary to save this attribute to the database. If no passed value is passed, then we save it via the stock form.
	 */
	protected function saveAttribute($attributeValue, $passedValue = false) {
		$at = $this->getAttributeType();
		$at->controller->setAttributeKey($this);
		$at->controller->setAttributeValue($attributeValue);
		if ($passedValue) {
			$at->controller->saveValue($passedValue);
		} else {
			$at->controller->saveForm($at->controller->post());
		}
		$at->__destruct();
		unset($at);
	}
	
	public function __destruct() {

	}
	
	public function validateAttributeForm($h = false) {
		$at = $this->getAttributeType();
		$at->controller->setAttributeKey($this);
		$e = true;
		if (method_exists($at->controller, 'validateForm')) {
			$e = $at->controller->validateForm($at->controller->post());
		}
		return $e;
	}


	public function createIndexedSearchTable() {
		if ($this->getIndexedSearchTable() != false) {
			$db = Loader::db();
			$dba = NewDataDictionary($db, DB_TYPE);
			$sqa = $dba->CreateTableSQL($this->getIndexedSearchTable(), $this->searchIndexFieldDefinition);
			$dba->ExecuteSQLArray($sqa);
		}
	}

	public function setAttributeSet($as) {
		if (!is_object($as)) {
			$as = AttributeSet::getByHandle($as);
		}
		$as->addKey($this);
	}
	
	public function clearAttributeSets() {
		$db = Loader::db();
		$db->Execute('delete from AttributeSetKeys where akID = ?', $this->akID);
	}
	
	public function getAttributeSets() {
		$db = Loader::db();
		$sets = array();
		$r = $db->Execute('select asID from AttributeSetKeys where akID = ?', $this->akID);
		while ($row = $r->FetchRow()) {
			$sets[] = AttributeSet::getByID($row['asID']);
		}
		return $sets;
	}
	
	/** 
	 * Saves an attribute using its stock form.
	 */
	public function saveAttributeForm($obj) {
		$this->saveAttribute($obj);
	}
	
	/** 
	 * Sets an attribute directly with a passed value.
	 */
	public function setAttribute($obj, $value) {
		$this->saveAttribute($obj, $value);
	}
	
	/** 
	 * @deprecated */
	public function outputSearchHTML() {
		$this->render('search');
	}
	
	// deprecated
	public function getKeyName() { return $this->getAttributeKeyName();}

	/** 
	 * Returns the handle for this attribute key
	 */
	public function getKeyHandle() { return $this->getAttributeKeyHandle();}
	
	/** 
	 * Returns the ID for this attribute key
	 */
	public function getKeyID() {return $this->getAttributeKeyID();}
	

}