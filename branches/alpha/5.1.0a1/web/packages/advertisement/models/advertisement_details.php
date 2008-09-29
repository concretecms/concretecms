<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('library_file');
Loader::model("advertisement_group", "advertisement");

class AdvertisementDetails extends Model {
	var $_table = 'btAdvertisementDetails';
	var $agIDs = array();
	
	
	function save($args) {
		if($args['aID'] > 0) {
			$this->load("aID=".$args['aID']);
		} else {
			$this->impressions = 0;
			$this->clickThrus = 0;
		}
		$this->agIDs		= $args['adGroupIDs'];
		$this->name 		= $args['name'];
		$this->fID 		= $args['fID'];
		$this->url 		= $args['url'];
		$this->html 		= $args['html'];
		$this->targetImpressions = $args['targetImpressions'];
		$this->targetClickThrus = $args['targetClickThrus'];
		$this->lastIndexed = $args['lastIndexed'];
	
		
		parent::save();
		$this->saveAdGroups($this->agIDs);
	}
	
	function getUrl() {
		return $this->url;
	}
	
	function load($args) {
		parent::load($args);
		$this->loadAdGroupIDs();
	}
	
	function detete() {
		$this->saveAdGroups(array());
		parent::delete();
	}

	function saveAdGroups($agArray) {
		$db = Loader::db();
		$db->query("delete from btAdvertisementToGroups where aID = ?", array($this->aID));
		if ($this->aID && is_array($agArray) && count($agArray)) {
			foreach($agArray as $agID) {
				$v = array($this->aID, $agID);
				$db->query("insert into btAdvertisementToGroups (aID, agID) values (?, ?)", $v);
			}
		}
	}

	function getFileObject() {
		return LibraryFileBlockController::getFile($this->fID);
	}
	
	
	function getAllGroups() {
		$ag = new AdvertisementGroup();
		return $ag->Find("TRUE ORDER BY agName");
	}
	
	
	function loadAdGroupIDs() {
		$db = Loader::db();
		$gIDs = array();
		if($this->aID) {
			$res = $db->query("SELECT agID FROM btAdvertisementToGroups WHERE aID = {$this->aID}");
			if($res->numRows()) {
				while($row = $res->fetchRow()) {
					$gIDs[] = $row['agID'];
				}
			}
		}
		$this->agIDs = $gIDs;
		return $this->agIDs;
	}
	
	function generateImpression() {
		$db = Loader::db();
		$v = array($this->aID);
		$q = "update btAdvertisementDetails set impressions = impressions + 1 where aID = ?";
		$r = $db->query($q, $v);
	}
	
	function generateClick() {
		$db = Loader::db();
		$v = array($this->aID);
		$q = "update btAdvertisementDetails set clickThrus = clickThrus + 1 where aID = ?";
		$r = $db->query($q, $v);
	}
	
	function getContentAndGenerate($alt, $align, $style, $id = null) {
		$db = Loader::db();
		$q = "select filename from btFile where bID = '{$this->fID}'";
		$r = $db->getOne($q);
		$fullPath = DIR_FILES_UPLOADED . '/' . $r;
		$size = @getimagesize($fullPath);
		$relPath = REL_DIR_FILES_UPLOADED . '/' . $r;
		
		$img = "<img border=\"0\" alt=\"{$alt}\" src=\"{$relPath}\" {$size[3]} ";
		$img .= ($align) ? "align=\"{$align}\" " : '';
		$img .= ($style) ? "style=\"{$style}\" " : '';
		$img .= ($id) ? "id=\"{$id}\" " : "";
		$img .= "/>";
		
		return $img;
	}

}
?>