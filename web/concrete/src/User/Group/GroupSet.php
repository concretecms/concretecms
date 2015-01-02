<?php
namespace Concrete\Core\User\Group;
use \Concrete\Core\Foundation\Object;
use Gettext\Translations;
use Loader;

class GroupSet extends Object {

	public static function getList() {
		$db = Loader::db();
		$r = $db->Execute('select gsID from GroupSets order by gsName asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['gsID']);
		}
		return $list;
	}

	public static function getByID($gsID) {
		$db = Loader::db();
		$row = $db->GetRow('select gsID, pkgID, gsName from GroupSets where gsID = ?', array($gsID));
		if (isset($row['gsID'])) {
			$gs = new static();
			$gs->setPropertiesFromArray($row);
			return $gs;
		}
	}

	public static function getByName($gsName) {
		$db = Loader::db();
		$row = $db->GetRow('select gsID, pkgID, gsName from GroupSets where gsName = ?', array($gsName));
		if (isset($row['gsID'])) {
			$gs = new static();
			$gs->setPropertiesFromArray($row);
			return $gs;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select gsID from GroupSets where pkgID = ? order by gsID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['gsID']);
		}
		$r->Close();
		return $list;
	}

	public function getGroupSetID() {return $this->gsID;}
	public function getGroupSetName() {return $this->gsName;}
	public function getPackageID() {return $this->pkgID;}

	/** Returns the display name for this group set (localized and escaped accordingly to $format)
	* @param string $format = 'html'
	*	Escape the result in html format (if $format is 'html').
	*	If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getGroupSetDisplayName($format = 'html') {
		$value = tc('GroupSetName', $this->getGroupSetName());
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}

	public function updateGroupSetName($gsName) {
		$this->gsName = $gsName;
		$db = Loader::db();
		$db->Execute("update GroupSets set gsName = ? where gsID = ?", array($gsName, $this->gsID));
	}

	public function addGroup(Group $g) {
		$db = Loader::db();
		$no = $db->GetOne("select count(gID) from GroupSetGroups where gID = ? and gsID = ?", array($g->getGroupID(), $this->getGroupSetID()));
		if ($no < 1) {
			$db->Execute('insert into GroupSetGroups (gsID, gID) values (?, ?)', array($this->getGroupSetID(), $g->getGroupID()));
		}
	}

	public static function add($gsName, $pkg = false) {
		$db = Loader::db();
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db->Execute('insert into GroupSets (gsName, pkgID) values (?,?)', array($gsName, $pkgID));
		$id = $db->Insert_ID();
		$gs = GroupSet::getByID($id);
		return $gs;
	}

	public function clearGroups() {
		$db = Loader::db();
		$db->Execute('delete from GroupSetGroups where gsID = ?', array($this->gsID));
	}

	public function getGroups() {
		$db = Loader::db();
		$r = $db->Execute('select gID from GroupSetGroups where gsID = ? order by gID asc', $this->getGroupSetId());
		$groups = array();
		while ($row = $r->FetchRow()) {
			$g = Group::getByID($row['gID']);
			if (is_object($g)) {
				$groups[] = $g;
			}
		}
		return $groups;
	}

	public function contains(Group $g) {
		$db = Loader::db();
		$r = $db->GetOne('select count(gID) from GroupSetGroups where gsID = ? and gID = ?', array($this->getGroupSetID(), $g->getGroupID()));
		return $r > 0;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from GroupSets where gsID = ?', array($this->getGroupSetID()));
		$db->Execute('delete from GroupSetGroups where gsID = ?', array($this->getGroupSetID()));
	}

	public function removeGroup(Group $g) {
		$db = Loader::db();
		$db->Execute('delete from GroupSetGroups where gsID = ? and gID = ?', array($this->getGroupSetID(), $g->getGroupID()));
	}

    public static function exportTranslations()
    {
        $translations = new Translations();
        $sets = static::getList();
        foreach($sets as $set) {
            $translations->insert('GroupSetName', $set->getGroupSetName());
        }
        return $translations;
    }

}
