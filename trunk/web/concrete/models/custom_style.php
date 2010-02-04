<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class CustomStyleRule extends Object {
	
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
				case 'border_position';	
				case 'border_color';	
				case 'border_style';	
					$tempStyles[$key]=$val;
					break;					
								
				case 'border_width';
				case 'padding_left';
				case 'padding_top';
				case 'padding_right';
				case 'padding_bottom';
				case 'margin_left';
				case 'margin_top';
				case 'margin_right';
				case 'margin_bottom';				
					if( !strlen(trim($val)) ) $val=0;
					if( strlen(trim($val))==strlen(intval($val)) && intval($val) )
						$val=intval($val).'px';
					$tempStyles[$key]=$val;
					break;
					
				case 'line_height';	
				case 'font_size';	
					if( !strlen(trim($val)) || !$val ) continue; 
					if( strlen(trim($val))==strlen(intval($val)) && intval($val) )
						$val=intval($val).'px';					
					$stylesStr.=str_replace('_','-',$key).':'.$val.'; ';
					break;
				
				case 'font_family';						
					if( $val=='inherit' ) continue;
					$val=self::$fontFamilies[$val];
					$stylesStr.=str_replace('_','-',$key).':'.$val.'; ';
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
	
	public function add($id, $class, $custom, $keys) {
		$cssData = array();
		$id = str_replace( array('"', "'", ';', "<", ">", "#"), '', $id);							
		$class = str_replace( array('"', "'", ';', "<", ">", "."), '', $class);
		$custom = str_replace( '"' , "'", $custom) ;	
	
		$styleKeys=array('font_family','color','font_size','line_height','text_align','background_color','border_style',
			'border_color','border_width','border_position','margin_top','margin_right','margin_bottom','margin_left',
			'padding_top','padding_right','padding_bottom','padding_left');
			
		$cssDataRaw=array();
		foreach($styleKeys as $styleKey){
			$cssDataRaw[$styleKey]=$keys[$styleKey];
		}
		
		$cssData = serialize($cssDataRaw);
		$db = Loader::db();
		$db->Execute('insert into CustomStyleRules (css_id, css_class, css_custom, css_serialized) values (?, ?, ?, ?)', array($id, $class, $custom, $cssData));
		$csrID = $db->Insert_ID();
		return CustomStyleRule::getByID($csrID);		
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
		$r = $db->GetRow('select * from CustomStyleRules where csrID = ?', array($csrID));
		if (is_array($r) && $r['csrID'] > 0) {
			$this->setPropertiesFromArray($r);
		}
	}
	
	
}

class CustomStylePreset extends Object {



}