<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CustomStyleRule extends Object {
	
	protected static $headerStylesAdded = false;
	public static $fontFamilies = array( 
		'inherit'=>'inherit', 
		'Arial'=>"Arial, Helvetica, sans-serif",
		'Times New Roman'=>"'Times New Roman', Times, serif",
		'Courier'=>"'Courier New', Courier, monospace",
		'Georgia'=>"Georgia, 'Times New Roman', Times, serif",
		'Verdana'=>"Verdana, Arial, Helvetica, sans-serif"		
	);			 
	protected $customStyleNameSpace = 'customStyle';
	
	public function getCustomStyleRuleID() {return $this->csrID;}
	public function getCustomStylePresetID() {
		return $this->cspID;
	}
	public function getCustomStyleRuleCSSID($withAutoID=false) {
		if( strlen(trim($this->css_id))) {
			return $this->css_id;
		} else if ($withAutoID && $this->getCustomStyleRuleID()) {
			return $this->customStyleNameSpace . $this->getCustomStyleRuleID();
		} else {
			return '';
		}
	}
	
	public function setCustomStyleNameSpace($ns) {
		$this->customStyleNameSpace = $ns;
	}

	public function getCustomStyleRuleClassName() {return $this->css_class;}
	public function getCustomStyleRuleCSSCustom() {return $this->css_custom;}
	public function getCustomStyleRuleCustomStylesArray() { // worst method name ever
		$styles = unserialize($this->css_serialized);
		if (!is_array($styles)) {
			return array();
		}
		return $styles;
	}
	
	public function getCustomStyleRuleText() {
		$stylesStr=''; 
		$tempStyles=array();
		$styles = $this->getCustomStyleRuleCustomStylesArray();
		foreach($styles as $key=>$val){
			if( !trim($key) ) continue;
			switch($key){ 					
				case 'border_position':	
				case 'border_color':	
				case 'border_style':	
					$tempStyles[$key]=$val;
					break;					
								
				case 'border_width':
				case 'padding_left':
				case 'padding_top':
				case 'padding_right':
				case 'padding_bottom':
				case 'margin_left':
				case 'margin_top':
				case 'margin_right':
				case 'margin_bottom':				
					if( !strlen(trim($val)) ) $val=0;
					if( strlen(trim($val))==strlen(intval($val)) && intval($val) )
						$val=intval($val).'px';
					$tempStyles[$key]=$val;
					break;
					
				case 'line_height':	
				case 'font_size':	
					if( !strlen(trim($val)) || !$val ) continue; 
					if( strlen(trim($val))==strlen(intval($val)) && intval($val) )
						$val=intval($val).'px';					
					$stylesStr.=str_replace('_','-',$key).':'.$val.'; ';
					break;
				
				case 'font_family':						
					if( $val=='inherit' ) continue;
					$val=self::$fontFamilies[$val];
					$stylesStr.=str_replace('_','-',$key).':'.$val.'; ';
					break;
				case 'background_image':
					if ($val > 0) {
						$bf = File::getByID($val);
						$stylesStr.=str_replace('_','-',$key).': url(\'' . $bf->getRelativePath() . '\'); ';
					}
					break;
				default:
					if( !strlen(trim($val)) ) continue;
					$stylesStr.=str_replace('_','-',$key).':'.$val.'; ';
			}
		}
		
		//shorthand approach to make the css a little tighter looking
		if( $tempStyles['margin_top'] || $tempStyles['margin_right'] || $tempStyles['margin_bottom'] || $tempStyles['margin_left'] ){
			$stylesStr.='margin:'.$tempStyles['margin_top'].' '.$tempStyles['margin_right'].' '.$tempStyles['margin_bottom'].' '.$tempStyles['margin_left'].'; ';
		}
		
		if( $tempStyles['padding_top'] || $tempStyles['padding_right'] || $tempStyles['padding_bottom'] || $tempStyles['padding_left'] ){
			$stylesStr.='padding:'.$tempStyles['padding_top'].' '.$tempStyles['padding_right'].' '.$tempStyles['padding_bottom'].' '.$tempStyles['padding_left'].'; ';
		}
		
		if( $tempStyles['border_width'] && $tempStyles['border_style']!='none' ){
			if($tempStyles['border_position']!='full') 
				$borderPos='-'.$tempStyles['border_position'];
			$stylesStr.='border'.$borderPos.':'.$tempStyles['border_width'].' '.$tempStyles['border_style'].' '.$tempStyles['border_color'].'; ';
		}				
		
		if( !strlen(trim($stylesStr)) && !strlen(trim($this->getCustomStyleRuleCSSCustom())) ) return '';
		$styleRules= str_replace( array("\n","\r"),'', $stylesStr.$this->getCustomStyleRuleCSSCustom() ); 
		return $styleRules;	
	}
	
	protected function sanitize($id, $class, $custom, $keys) {
		$cssData = array();
		$id = str_replace( array('"', "'", ';', "<", ">", "#"), '', $id);							
		$class = str_replace( array('"', "'", ';', "<", ">", "."), '', $class);
		$custom = str_replace( '"' , "'", $custom) ;	
	
		$styleKeys=array('font_family','color','font_size','line_height','text_align','background_color','border_style',
			'border_color','border_width','border_position','margin_top','margin_right','margin_bottom','margin_left',
			'padding_top','padding_right','padding_bottom','padding_left', 'background_image', 'background_repeat');
			
		$cssDataRaw=array();
		foreach($styleKeys as $styleKey){
			$cssDataRaw[$styleKey]=$keys[$styleKey];
		}
		
		$cssData = serialize($cssDataRaw);
		
		$obj = new stdClass;
		$obj->id = $id;
		$obj->class = $class;
		$obj->custom = $custom;
		$obj->cssData = $cssData;
		return $obj;
	}
	
	public function add($id, $class, $custom, $keys) {
		$obj = CustomStyleRule::sanitize($id, $class, $custom, $keys);
		$db = Loader::db();
		$db->Execute('insert into CustomStyleRules (css_id, css_class, css_custom, css_serialized) values (?, ?, ?, ?)', array($obj->id, $obj->class, $obj->custom, $obj->cssData));
		$csrID = $db->Insert_ID();
		return CustomStyleRule::getByID($csrID);		
	}
	
	public function update($id, $class, $custom, $keys) {
		$obj = $this->sanitize($id, $class, $custom, $keys);
		$db = Loader::db();
		$db->Execute('update CustomStyleRules set css_id = ?, css_class = ?, css_custom = ?, css_serialized = ? where csrID = ?', array($obj->id, $obj->class, $obj->custom, $obj->cssData, $this->getCustomStyleRuleID()));
	}
	
	public function getByID($csrID) {
		$csr = new CustomStyleRule();
		$csr->load($csrID);
		if (is_object($csr) && $csr->getCustomStyleRuleID() == $csrID) {
			return $csr;
		}
	}
	
	public function load($csrID) {
		$db = Loader::db();
		$r = $db->GetRow('select CustomStyleRules.*, CustomStylePresets.cspID from CustomStyleRules left join CustomStylePresets on CustomStyleRules.csrID = CustomStylePresets.csrID where CustomStyleRules.csrID = ?', array($csrID));
		if (is_array($r) && $r['csrID'] > 0) {
			$this->setPropertiesFromArray($r);
		}
	}
	
	
}

class Concrete5_Model_CustomStylePreset extends Object {

	public function getList() {
		$db = Loader::db();
		$r = $db->Execute('select cspID, cspName, csrID from CustomStylePresets order by cspName asc');
		$presets = array();
		while ($row = $r->FetchRow()) {
			$obj = new CustomStylePreset();
			$obj->setPropertiesFromArray($row);
			$presets[] = $obj;
		}
		return $presets;
	}

	public function getCustomStylePresetID() {return $this->cspID;}
	public function getCustomStylePresetName() {return $this->cspName;}
	public function getCustomStylePresetRuleID() {return $this->csrID;}
	public function getCustomStylePresetRuleObject() {return CustomStyleRule::getByID($this->csrID);}

	public static function getByID($cspID) {
		$csp = new CustomStylePreset();
		$csp->load($cspID);
		if (is_object($csp) && $csp->getCustomStylePresetID() == $cspID) {
			return $csp;
		}
	}
	
	public function load($cspID) {
		$db = Loader::db();
		$r = $db->GetRow('select cspID, cspName, csrID from CustomStylePresets where cspID  = ?', array($cspID));
		if (is_array($r) && $r['cspID'] > 0) {
			$this->setPropertiesFromArray($r);
		}
	}
	
	/** 
	 * Removes a preset. Does NOT remove the associated rule
	 */
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CustomStylePresets where cspID = ?', array($this->cspID));
	}
	
	public function add($cspName, $csr) {
		$db = Loader::db();
		$db->Execute('insert into CustomStylePresets (cspName, csrID) values (?, ?)', array(
			$cspName,
			$csr->getCustomStyleRuleID()
		));
	
	}
	
}