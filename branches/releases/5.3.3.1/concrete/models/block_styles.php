<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Users
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */


class BlockStyles extends Object {

	protected $bID=0;
	protected $cID=0;
	protected $css_data=array();
	protected $unserialized_styles=array();
	protected $db;	
	protected static $headerStylesAdded=0;
	
	//adds styles to header for all blocks with this cID
	static public function addHeaderItems($c){
		if( self::$headerStylesAdded || !intval($c->cID) ) return;
		
		//check the cache for the master list of this page version's blockStyles 
		$blockStylesObjs = Cache::get( 'pagesBlockStyles', intval($c->cID).'_'.intval($c->getVersionID()) );
		if( !is_array($blockStylesObjs) ){	 
			
			//query this pages block style records
			$blockStylesObjs=array(); 
			$db = Loader::db(); 
			$sql = 'SELECT bs.*, b.bID, cvb.cID FROM CollectionVersionBlocks AS cvb INNER JOIN Blocks AS b ON (cvb.bID = b.bID) 
					LEFT JOIN CollectionVersionBlockStyles AS bs ON bs.bID=b.bID
					WHERE cvb.cID = ? AND (cvb.cvID = ? OR cvb.cbIncludeAll=1)'; 
			$blockStylesData = $db->getAll( $sql, array( $c->cID, $c->getVersionID() ) ); 			
			
			//load the block style objects
			foreach($blockStylesData as $data){ 
				$blockStyles = new BlockStyles();
				$blockStyles->setData( $data ); 
				$blockStylesObjs[]=$blockStyles;
			}
			
			Cache::set( 'pagesBlockStyles', intval($c->cID).'_'.intval($c->getVersionID()), $blockStylesObjs, false, true); 
		}
		
		//get the header style rules
		$blockStylesHeader='';
		foreach($blockStylesObjs as $blockStyles){
			$blockStyles->addToRuntimeCache(); 
			if($blockStyles->getCssID(1)) 
				$blockStylesHeader.='#'.$blockStyles->getCssID(1).' {'.$blockStyles->getStyleRules()."} \r\n";  
		} 
		
		//add to header
		$v = View::getInstance();
		$v->addHeaderItem("<style> \r\n".$blockStylesHeader.'</style>', 'CONTROLLER');			
		self::$headerStylesAdded=1;
	} 
	
	//This runtime cache should load all the block styles with just one db call :-) 
	//(or with just one file system call if that query is ROM cached, which it should be) 
	protected static $runtimeCache=array(); 
	static public function retrieveFromRuntimeCache( $bID=0, $cID=0 ){ 
		if(!intval($bID) || !intval($cID)) return; 
		foreach(BlockStyles::$runtimeCache as $bs){ 
			if ( $bs->getCID()==$cID && $bs->getBID()==$bID )
				return $bs; 
		} 			
	}	
	protected function addToRuntimeCache(){
		self::$runtimeCache[ $this->getCID().'-'.$this->getBID() ] = $this;	
	}
	
	static public function retrieve( $bID, $c ){
		$cID = intval($c->cID);
		if(!intval($bID) || !$cID ) return; 
	 		
		$cachedObj=self::retrieveFromRuntimeCache( $bID, $cID);
		if( $cachedObj ) return $cachedObj; 
	
		$db = Loader::db();	
		$vals = array( intval($bID), $cID );
		$sql = 'SELECT * FROM CollectionVersionBlockStyles WHERE bID=? AND cID=?';
		$blockStylesData = $db->getRow($sql,$vals);
		if( !$blockStylesData || !count($blockStylesData) ) return false; 
		$blockStyles = new BlockStyles();
		$blockStyles->setData( $blockStylesData ); 
		$blockStyles->addToRuntimeCache();
		
		return $blockStyles;
	}	
	
	public function __construct(){
		$this->db = Loader::db();	
	}
	
	public function getID(){ return intval($this->cvbsID); }
	public function getBID(){ return intval($this->bID); }
	public function setBID($v){ $this->bID = intval($v); }
	public function getCID(){ return intval($this->cID); }
	public function setCID($v){ $this->cID = intval($v); }	
	
	public function getCssID($withAutoID=false){
		if( strlen(trim($this->css_data['css_id'])) ) 
			 return trim($this->css_data['css_id']);
		elseif($withAutoID && $this->getID()) return 'blockStyles' . $this->getID();
		else return '';
	}
	
	public function getClassName(){
		if(strlen(trim($this->css_data['css_class']))) 
			return trim($this->css_data['css_class']).' ';
		else return '';
	}
	
	//these are the defined styles
	public function getStylesArray(){
		if(is_array($this->unserialized_styles)) 
			 return $this->unserialized_styles;
		else return array();
	}
	
	//other handcoded styles
	public function getCustomCSS(){
		return $this->css_data['css_custom'];
	}
	
	public static $fontFamilies = array( 
		'inherit'=>'inherit', 
		'Arial'=>"Arial, Helvetica, sans-serif",
		'Times New Roman'=>"'Times New Roman', Times, serif",
		'Courier'=>"'Courier New', Courier, monospace",
		'Georgia'=>"Georgia, 'Times New Roman', Times, serif",
		'Verdana'=>"Verdana, Arial, Helvetica, sans-serif"		
	);			 
	
	public function getStyleRules(){
		$stylesStr=''; 
		$tempStyles=array();
		$styles=$this->getStylesArray();
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
		
		if( !strlen(trim($stylesStr)) && !strlen(trim($this->getCustomCSS())) ) return '';
		$styleRules= str_replace( array("\n","\r"),'', $stylesStr.$this->getCustomCSS() ); 
		return $styleRules;
	}
	
	//adds or updates
	public function save( $c ){
		if( !intval( $this->bID ) ) return false;
		if( !intval( $c->cID ) ) return false;
		
		$css = $this->css_data;
		$vals = array( str_replace('.','',$css['css_class']).'', str_replace('#','',$css['css_id']).'', $css['css_serialized'].'', $css['css_custom'].'', $c->cID, $this->bID );
		if( self::recordExists($this->bID,$c->cID) ){
			$sql = 'UPDATE CollectionVersionBlockStyles SET css_class=?, css_id=?, css_serialized=?, css_custom=? WHERE cID=? AND bID=?'; 
		}else{  
			$sql = 'INSERT INTO CollectionVersionBlockStyles ( css_class, css_id, css_serialized, css_custom, cID, bID ) values (?, ?, ?, ?, ?, ?)'; 
		}			
		$this->db->query($sql,$vals);		
		$this->addToRuntimeCache();
		
		//remove this pages block styles cache
		Cache::delete( 'pagesBlockStyles', intval($c->cID).'_'.intval($c->getVersionID())  ); 
		
		return true;
	}	
	
	static public function recordExists($bID,$cID){
		$db = Loader::db();	
		$vals = array(intval($bID),intval($cID));
		$sql = 'SELECT count(*) FROM CollectionVersionBlockStyles WHERE bID=? AND cID=?';
		return ( intval($db->getOne($sql,$vals))>0 ) ? true : false; 
	}
	
	public function setData( $data=array() ){
		$this->bID=intval($data['bID']);
		$this->cID=intval($data['cID']);
		$this->cvbsID = intval($data['cvbsID']);
		
		$this->css_data['css_id']=$data['css_id'];
		$this->css_data['css_class']=$data['css_class'];
		$this->css_data['css_custom']=$data['css_custom'];
		
		if( $data['css_unserialized'] ){
			$this->unserialized_styles=$data['css_unserialized'];
			$this->css_data['css_serialized']=serialize($this->unserialized_styles);
		}else{
			$this->unserialized_styles = unserialize($data['css_serialized']);
			$this->css_data['css_serialized'] = $data['css_serialized'];
		}
	}	
	

	/*
	public function hasPadding(){
		$us=$this->unserialized_styles;
		if( strlen(trim($us['padding_top'])) ) return true;
		if( strlen(trim($us['padding_right'])) ) return true;
		if( strlen(trim($us['padding_bottom'])) ) return true;
		if( strlen(trim($us['padding_left'])) ) return true;
		return false;
	}
	*/ 	
}

?>