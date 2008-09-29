<?
/** 
 * AdBlockController 
 * classes for managing the ad block
 *
 * @package blocks
 * @subpackage advertisement
 */

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model("advertisement_details", "advertisement");

	class AdvertisementBlockController extends BlockController {
		
		/** 
		* @var object
		*/
		var $pobj;
		
		protected $btDescription = "Banner Advertisement System";
		protected $btName = "Advertisement";
		protected $btTable = 'btAdvertisement';
		protected $btInterfaceWidth = "420";
		protected $btInterfaceHeight = "480";	
		protected $btIncludeAll = 1;
		public $adGroups = array();	

		function __construct($obj = NULL) {
			parent::__construct($obj);
			
			if($this->bID) {
				$this->ad = new AdvertisementDetails();
				if($this->aID) {
					$this->ad->load("aID=".$this->aID);
				}
			}
		}
				
		function getUrl() { return $this->ad->url; }
	
		function inGroup($g) {
			return in_array($g->getAdGroupID(), $this->adGroups);
		}
		
		function getAdGroups() {
			$db = Loader::db();
			
			$r = (isset($this->bID)) ? $db->query("select btAdGroups.agID, agName from btAdGroups inner join btAdsToGroups on btAdGroups.agID = btAdsToGroups.agID inner join btAd on btAdsToGroups.bID = btAd.bID where btAd.bID = {$this->bID}") : $db->query("select agID, agName from btAdGroups order by agID asc");
			$groups = array();
			while ($row = $r->fetchRow()) {
				$ag = new BlockAdGroup;
				$ag->agID = $row['agID'];
				$ag->agName = $row['agName'];
				$groups[] = $ag;
			}
			return $groups;			
		}
		
		function getAllGroups() {
			return $this->ad->getAllGroups();
		}
		
		function setRemainingImpressions($impressions) {
			$this->remainingImpressions = $impressions;
		}
		
		function setRemainingImpressionsFloor($impressions) {
			$this->remainingImpressionsFloor = $impressions;
		}
		
		function getRemainingImpressionsModifier() {
			$impModifier = $this->remainingImpressions / $this->remainingImpressionsFloor;
			$impModifier = round($impModifier, 4) * 10000;
			return $impModifier;
		}
		
		function setAdGroups($agArray) {
			$db = Loader::db();
			$db->query("delete from btAdsToGroups where bID = ?", array($this->bID));
			if ($this->bID && is_array($agArray)) {
				foreach($agArray as $agID) {
					$v = array($this->bID, $agID);
					$db->query("insert into btAdsToGroups (bID, agID) values (?, ?)", $v);
				}
			}
		}
		
				
		function generateImpression(&$c) {
			$this->ad->generateImpression();
		}
		
		function generateClick(&$c) {
			$this->ad->generateClick();
		}
		
		function getContentAndGenerate($alt, $align, $style, $id = null) {
			return $this->ad->getContentAndGenerate($alt, $align, $style, $id);
		}

		function delete() {
			parent::delete();
		}
	
		function save($args) {
			if(!is_object($this->ad)) {
				$this->ad = new AdvertisementDetails();
			}
			if($args['ad_source'] == "new") {
				$this->ad = new AdvertisementDetails();
				$this->ad->save($args);
				$blockArgs = array("aID"=>$this->ad->aID,"agID"=>0,"numAds"=>0);
			} else { // existing ad source;
				if($args['existing_source'] == 'single') {
					$blockArgs = array("aID"=>$args['existing_aID'],"agID"=>0,"numAds"=>0);	
				} else {
					$blockArgs = array("aID"=>0,"agID"=>$args['existing_agID'],"numAds"=>$args['numAds']);	
				}
			}	
			parent::save($blockArgs);
		}
	
	
	} // end class def

?>