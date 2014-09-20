<?php
namespace Concrete\Core\Conversation\Rating;
use Concrete\Core\Conversation\Message\Message;
use \Concrete\Core\Foundation\Object;
use Loader;
use \Concrete\Core\Package\PackageList;
use Core;
abstract class Type extends Object {

	abstract public function outputRatingTypeHTML();
	abstract public function adjustConversationMessageRatingTotalScore(Message $message);
	abstract public function rateMessage();
	public function getRatingTypeHandle()
    {
        return $this->cnvRatingTypeHandle;
    }
    public function getRatingTypeID()
    {
        return $this->cnvRatingTypeID;
    }

	/** Returns the list of all conversation rating types
	* @return array[Type]
	*/
	public static function getList() {
		$db = Loader::db();
		$handles = $db->GetCol('select cnvRatingTypeHandle from ConversationRatingTypes order by cnvRatingTypeHandle asc');
		$types = array();
		foreach($handles as $handle) {
			$ratingType = static::getByHandle($handle);
			if (is_object($ratingType)) {
				$types[] = $ratingType;
			}
		}
		return $types;
	}

	public static function add($cnvRatingTypeHandle, $cnvRatingTypeName, $cnvRatingTypeCommunityPoints, $pkgID = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into ConversationRatingTypes (cnvRatingTypeHandle, cnvRatingTypeName, cnvRatingTypeCommunityPoints, pkgID) values (?, ?, ?, ?)', array($cnvRatingTypeHandle, $cnvRatingTypeName, $cnvRatingTypeCommunityPoints, $pkgID));
		return static::getByHandle($cnvRatingTypeHandle);
	}

	public static function getByHandle($cnvRatingTypeHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvRatingTypeID, cnvRatingTypeHandle, cnvRatingTypeName, cnvRatingTypeCommunityPoints, pkgID from ConversationRatingTypes where cnvRatingTypeHandle = ?', array($cnvRatingTypeHandle));
		
		if (is_array($r) && $r['cnvRatingTypeHandle']) {
            $class = '\\Concrete\\Core\\Conversation\\Rating\\' . Loader::helper('text')->camelcase($r['cnvRatingTypeHandle']) . 'Type';
			$sc = Core::make($class);
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}
	
	public static function getByID($cnvRatingTypeID) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvRatingTypeID, cnvRatingTypeHandle, cnvRatingTypeName, cnvRatingTypeCommunityPoints, pkgID from ConversationRatingTypes where cnvRatingTypeID = ?', array($cnvRatingTypeID));
		
		if (is_array($r) && $r['cnvRatingTypeHandle']) {
            $class = '\\Concrete\\Core\\Conversation\\Rating\\' . Loader::helper('text')->camelcase($r['cnvRatingTypeHandle']) . 'Type';
			$sc = Core::make($class);
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}

	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('conversationratingtypes');
		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('conversationratingtype');
			$type->addAttribute('handle', $sc->getConversationRatingTypeHandle());
			$type->addAttribute('name', $sc->getConversationRatingTypeName());
			$type->addAttribute('package', $sc->getPackageHandle());
			$type->addAttribute('points', $sc->getConversationRatingTypePoints());
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$handles = $db->GetCol('select cnvRatingTypeHandle from ConversationRatingTypes where pkgID = ? order by cnvRatingTypeHandle asc', array($pkg->getPackageID()));
		$types = array();
		foreach($handles as $handle) {
			$ratingType = static::getByHandle($handle);
			if (is_object($ratingType)) {
				$types[] = $ratingType;
			}
		}
		return $types;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ConversationRatingTypes where cnvRatingTypeHandle = ?', array($this->cnvRatingTypeHandle));
	}


	public function getConversationRatingTypeHandle() {return $this->cnvRatingTypeHandle;}
	public function getConversationRatingTypeName() {return $this->cnvRatingTypeName;}
	public function getConversationRatingTypeID() {return $this->cnvRatingTypeID;}
	public function getConversationRatingTypePoints() {return $this->cnvRatingTypeCommunityPoints;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}
    /** Returns the display name for this instance (localized and escaped accordingly to $format)
    * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
    * @return string
    */
    public function getConversationRatingTypeDisplayName($format = 'html')
    {
        $value = tc('ConversationRatingTypeName', $this->cnvRatingTypeName);
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }
}