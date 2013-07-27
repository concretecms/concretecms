<? 

/**
 * @package Users
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 
 
 
 
 NOTES:
 
 fix problem with clicking on slider, and with delayed drag update  
 
 move layout down -> blocks in area below should pop off into new layout   
 
 */
 
 
 
 class Concrete5_Model_Layout extends Object {
	 
	public static $tableName='Layout';
	
	public $layoutID=0;
	//public $layoutName='Layout';
	public $type='table';
	public $columns=3;
	public $spacing=0;
	public $rows=3;	
	public $locked=0;	
	public $layoutTypes=array('area','table','columns','itemlist','staggered');
	public $breakpoints=array();
	public $areaNameNumber=0;
	public $parentAreaAttributes;
	
	
	//position and cvalID are properties of the collectionVersionAreaLayout join, set when being loaded for an area, not with layout object itself 
	public $position=1000;
	public $cvalID=0;
	
	function __construct( $params=array() ){ 
		$this->fill($params); 
	}	
	
	public function getLayoutID(){ return intval($this->layoutID); }
	
	public function setAreaNameNumber($v){ $this->areaNameNumber = intval($v); }
	public function getAreaNameNumber(){ return intval($this->areaNameNumber); } 
	
	public function getLayoutNameDivider(){ return ' : '; }
	public function getLayoutNameTxt(){ return 'Layout'; }
	public function getLayoutName(){ return $this->getAreaHandle().$this->getLayoutNameDivider().$this->getLayoutNameTxt().' '.$this->getAreaNameNumber(); }
	public function getLayoutPresetId(){ return intval($this->lpID); }
	public function getLayoutPresetObj(){ return LayoutPreset::getByID($this->lpID); }
	
	public static function getById( $layoutID ){ 
		if(!intval($layoutID) ) return false; 
			
		//$cachedObj=self::retrieveFromRuntimeCache( $layoutID );
		//if( $cachedObj ) return $cachedObj;  
	
		$db = Loader::db();	
		$vals = array( intval($layoutID) );
		$sql = 'SELECT l.*, lp.lpName, lp.lpID FROM Layouts AS l LEFT JOIN LayoutPresets AS lp ON l.layoutID = lp.layoutID WHERE l.layoutID=?';
		$data = $db->getRow($sql,$vals); 
		if( !$data || !count($data) ) return false;  
		$layout = new Layout( $data ); 
		//$layout->addToRuntimeCache();
		
		return $layout;
	}
	
	//when editing a layout, it should be the only one tied to that collection version. Used in process->atask=layout->edit 
	public function isUniqueToCollectionVersion($c){
		$db = Loader::db();	
		$vals = array( intval($c->getCollectionID()), $c->getVersionID(), $this->getLayoutID() );
		$sql = 'SELECT count(*) FROM CollectionVersionAreaLayouts WHERE cID=? AND cvID=? AND layoutID=?';
		return ( intval($db->getOne($sql,$vals))==1) ? true:false; 
	}
	
	//breakpoints an optional array of percentages, for the break points between columns, 
	//for a three column layout, you could for instance set the column breaks like array('25%','75%')
	function fill( $params=array( 'layoutID'=>0, 'type'=>'table','rows'=>3,'columns'=>3, 'breakpoints'=>array(), 'locked'=>0, 'lpID'=>0, 'lpName'=>'', 'spacing'=>0 ) ){  
		
		$this->layoutID=intval($params['layoutID']); 
		$this->locked=intval($params['locked']); 
		$this->type = (!in_array($params['type'],$this->layoutTypes))?'table':$params['type'];
		
		if(intval($params['layout_rows'])) $this->rows=intval($params['layout_rows']); 
		else $this->rows=(intval($params['rows'])<1)?1:$params['rows']; 
		
		if(intval($params['layout_columns'])) $this->columns=intval($params['layout_columns']); 
		else $this->columns=(intval($params['columns'])<1)?3:$params['columns']; 
		
		if(intval($params['areaNameNumber'])) $this->areaNameNumber = intval($params['areaNameNumber']);  
		
		$this->lpID=intval($params['lpID']);
		$this->lpName=$params['lpName'];
		
		if( strlen($params['spacing']) ) $this->spacing=$params['spacing'];
		
		if( !is_array($params['breakpoints']) && strlen(trim($params['breakpoints'])) ) $this->breakpoints = explode(',',$params['breakpoints']); 
		elseif(is_array($params['breakpoints']) && (count($params['breakpoints']) || $this->columns==1)) $this->breakpoints=$params['breakpoints']; 
		
		$this->cleanBreakPoints(); 
	}

	public function setAreaObj($a){
		$this->areaObj=$a;
	}
	
	public function getAreaObj(){  
		if(!$this->areaObj) throw new Exception( t('Error: no area assigned to layout') );
		return $this->areaObj;
	}
	
	
	public function getAreaHandle(){
		$a = $this->getAreaObj();
		if(is_object($a)) return $a->getAreaHandle();
		return '';
	}
	
	
	
	//adds or updates
	public function save(){ 
		
		if( !is_array($this->breakpoints) ) $this->breakpoints = explode(',',$this->breakpoints); 
		$vals = array( intval($this->columns), intval($this->rows), intval($this->locked), join(',',$this->breakpoints), $this->spacing );
		
		
		if( intval($this->layoutID) ){ 
			$sql = 'UPDATE Layouts SET layout_columns=?, layout_rows=?, locked=?, breakpoints=?, spacing=? WHERE layoutID=' . $this->getLayoutId() ; 
		}else{   
			$sql = 'INSERT INTO Layouts ( layout_columns, layout_rows, locked, breakpoints, spacing ) values (?, ?, ?, ?, ?)'; 
		}			
		
		$db = Loader::db();
		$db->query($sql,$vals);	
		
		if( !$this->getLayoutId() ) 
			$this->layoutID = intval($db->Insert_ID()); 
		
		//$this->addToRuntimeCache();
		
		//remove from cache
		//Cache::delete( 'pagesBlockStyles', intval($c->cID).'_'.intval($c->getVersionID())  ); 
		
		return true;
	}	
	
	protected function cleanBreakPoints(){
		$cleanBreakPoints=array();
		foreach($this->breakpoints as $breakPoint){
			if( floatval($breakPoint) )
				$cleanBreakPoints[]=floatval($breakPoint).'%';
		}
		$this->breakpoints=$cleanBreakPoints; 
		
		if( count($this->breakpoints)==0 || (count($this->breakpoints)!=($this->columns-1)) ){  
			$this->setDefaultBreaks(); 
		} 
	}	
	
	function setDefaultBreaks(){  
		$colWidth=100/$this->columns;
		$this->breakpoints=array(); 
		for( $i=1; $i<($this->columns); $i++ ) 
			$this->breakpoints[] = ($i*$colWidth).'%';  
	}
	
	function display( $c=NULL, $a=NULL ){ 
	
		if(!$c) global $c;
		
		if(!$a) global $a;

		if($a instanceof Area) {
			$this->parentAreaAttributes = $a->attributes;
		}
		
		if(!in_array($this->type,$this->layoutTypes)) $this->layoutType='table'; 
		
		echo '<div id="ccm-layout-wrapper-'.intval($this->cvalID).'" class="ccm-layout-wrapper">';
		
		if ($c->isEditMode()) { 
			$args = array('layout'=>$this);
			Loader::element('block_area_layout_controls', $args); 
		}
		
		//echo intval($this->cvalID).' '.$this->layoutID.'<br>';
		
		$this->displayTableGrid($this->rows,$this->columns,$c); 
		/*
		switch($this->type){		
			case 'staggered':
				$this->displayStaggeredGrid($this->rows);
				break;
			case 'itemlist':
				$this->displayItemListGrid($this->rows);
				break;							
			case 'columns':
				$this->displayColumnsGrid($this->columns);
				break;				
			case 'table':
				$this->displayTableGrid($this->rows,$this->columns);
				break;				
			case 'area':
			default:
				$this->displayAreaGrid();
		}
		*/ 
		
		echo '</div>';
	}
	
	protected function getNextColWidth($zeroIndexedColNum=0,$cumulativeWidth=0){
		$j=$zeroIndexedColNum;
		if( $j < count($this->breakpoints) ){
			$colWidth = intval($this->breakpoints[$j]) - $cumulativeWidth;
			$colWidth.= (strstr($this->breakpoints[$j],'%')) ? '%' : 'px'; 
			//$cumulativeWidth += intval(str_replace(array('px','%'),'',strtolower($colWidth)));
		}else{			
			if( strstr($this->breakpoints[ count($this->breakpoints)-1 ],'%') ){
				//echo $cumulativeWidth;
				$colWidth =  (99.99 - $cumulativeWidth);
				$colWidth .= '%';
			}else{
				$colWidth =  $this->breakpoints[ count($this->breakpoints)-1 ] . 'px';
			}
		}
		return $colWidth;
	}
	/*
	protected function displayAreaGrid($c=NULL){
		if(!$c) global $c;  
		if($c->isEditMode()) $editMode='ccm-edit-mode';
		echo '<div id="ccm-layout-'.$this->layoutID.'" class="ccm-layout ccm-layout-type-area ccm-layout-name-'.TextHelper::camelcase($this->layoutName).'">';
			echo '<div class="ccm-layout-row">'; 
				echo '<div class="ccm-layout-cell">';
				$a = new Area($this->layoutName.' '.t('Cell').' '.$this->getCellNumber());
				$a->display($c);			
				echo '</div>';	
				echo '<div class="ccm-spacer"></div>';			
			echo '</div>';
		echo '</div>';
	}	
	*/ 
	
	public function getCellAreaHandle( $cellNumber=0 ){  
		return $this->getLayoutName().$this->getLayoutNameDivider().'Cell '.intval($cellNumber); 
	}
	
	public function getMaxCellNumber(){ 
		return intval($this->rows)*intval($this->columns);
	}
	
	protected function displayTableGrid($rows=3,$columns=3,$c=NULL){
		if(!$c) global $c;
		if($c->isEditMode()) $editMode='ccm-edit-mode';
		if(!intval($rows)) $rows=1;
		if(!intval($columns)) $columns=3;
		$layoutNameClass = 'ccm-layout-name-'.TextHelper::camelcase($this->getAreaHandle()).'-'.TextHelper::camelcase($this->getLayoutNameTxt()).'-'.$this->getAreaNameNumber();
		$layoutIDVal = strtolower('ccm-layout-'.TextHelper::camelcase($this->getAreaHandle()).'-'.$this->layoutID . '-'. $this->getAreaNameNumber());
		echo '<div id="'.$layoutIDVal.'" class="ccm-layout ccm-layout-table  '.$layoutNameClass.' '.$editMode.'">';
		for( $i=0; $i<$rows; $i++ ){
			echo '<div class="ccm-layout-row ccm-layout-row-'.($i+1).'">';
				$cumulativeWidth=0;
				for( $j=0; $j<$columns; $j++ ){	 
					$colWidth=($columns==1)?'100%':$this->getNextColWidth($j,$cumulativeWidth);
					$cumulativeWidth += intval(str_replace(array('px','%'),'',strtolower($colWidth)));
					$columnn_id = 'ccm-layout-'.intval($this->layoutID).'-col-'.($j+1);
					
					if($j==0) $positionTag='first';
					elseif($j==($columns-1)) $positionTag='last';
					else $positionTag = '';
					
					echo '<div class="'.$columnn_id.' ccm-layout-cell ccm-layout-col ccm-layout-col-'.($j+1).' '.$positionTag.'" style="width:'.$colWidth.'">';
					$a = new Area( $this->getCellAreaHandle($this->getCellNumber()) );
					$a->attributes = $this->parentAreaAttributes;
					ob_start();
					$a->display($c);			
					$areaHTML = ob_get_contents();
					ob_end_clean(); 
				
					if(strlen($areaHTML)){
						if( intval($this->spacing) )  
							$areaHTML='<div class="ccm-layout-col-spacing">'.$areaHTML.'</div>';							
						echo $areaHTML; 
					}
					else echo '&nbsp;';
					echo '</div>';				
				}
				echo '<div class="ccm-spacer"></div>';			
			echo '</div>';
		}
		echo '</div>';
	}	
	/* 
	protected function displayColumnsGrid($columns=3,$c=NULL){
		if(!$c) global $c;
		if($c->isEditMode()) $editMode='ccm-edit-mode';
		if(!intval($columns)) $columns=3; 
		echo '<div id="ccm-layout-'.$this->layoutID.'" class="ccm-layout ccm-layout-type-columns ccm-layout-name-'.TextHelper::camelcase($this->getLayoutName()).' '.$editMode.'">';
			echo '<div class="ccm-layout-row">';
				$cumulativeWidth=0;
				for( $j=0; $j<$columns; $j++ ){	 
					$colWidth=$this->getNextColWidth($j,$cumulativeWidth);
					$cumulativeWidth += intval(str_replace(array('px','%'),'',strtolower($colWidth))); 
					$endColClass=(($j+1)==$columns)?'ccm-layout-cell-col-last':'';					
					echo '<div class="ccm-layout-cell ccm-layout-col ccm-layout-col-'.($j+1).' '.$endColClass.'" style="width:'.$colWidth.'">';
					$a = new Area($this->getLayoutName().' Cell '.$this->getCellNumber());
					$a->display($c);			
					echo '</div>';									
				}
				echo '<div class="ccm-spacer"></div>';
			echo '</div>';
		echo '</div>';
	}
	
	protected function displayItemListGrid($rows=3, $c=NULL){ 
		if(!$c) global $c;
		if($c->isEditMode()) $editMode='ccm-edit-mode';
		if(!intval($rows)) $rows=3;
		echo '<div id="ccm-layout-'.$this->layoutID.'" class="ccm-layout ccm-layout-type-itemlist ccm-layout-name-'.TextHelper::camelcase($this->getLayoutName()).' '.$editMode.'">';
		$cumulativeWidth=0;
		$colWidth=$this->getNextColWidth(0,$cumulativeWidth);
		$cumulativeWidth += intval(str_replace(array('px','%'),'',strtolower($colWidth)));
		$colWidth2=$this->getNextColWidth(1,$cumulativeWidth);
		for( $i=0; $i<$rows; $i++ ){				
			echo '<div class="ccm-layout-row ccm-layout-row-'.($i+1).'">';
				echo '<div class="ccm-layout-cell ccm-layout-cell-left" style="width:'.$colWidth.'">';
					$a = new Area($this->getLayoutName().' Cell '.$this->getCellNumber());
					$a->display($c); 			
				echo '</div>';
				echo '<div class="ccm-layout-cell ccm-layout-cell-right" style="width:'.$colWidth2.'">';
					$a = new Area($this->getLayoutName().' Cell '.$this->getCellNumber());
					$a->display($c);			
				echo '</div>';	
				echo '<div class="ccm-spacer"></div>';			
			echo '</div>';
		}
		echo '</div>';
	}		
	
	protected function displayStaggeredGrid($rows=3, $c=NULL){
		if(!$c) global $c;
		if($c->isEditMode()) $editMode='ccm-edit-mode';
		if(!intval($rows)) $rows=3;
		echo '<div id="ccm-layout-'.$this->layoutID.'" class="ccm-layout ccm-layout-type-staggered ccm-layout-name-'.TextHelper::camelcase($this->getLayoutName()).' '.$editMode.'">';
		$colWidth=$this->getNextColWidth(0,0);
		$cumulativeWidth1 = intval(str_replace(array('px','%'),'',strtolower($colWidth))); 
		$colWidth2=$this->getNextColWidth(1,$cumulativeWidth1);
		$cumulativeWidth2 = $cumulativeWidth1 + intval(str_replace(array('px','%'),'',strtolower($colWidth2))); 
		$colWidth3=$this->getNextColWidth(2,$cumulativeWidth2); 
		$cumulativeWidth3 = $cumulativeWidth2 + intval(str_replace(array('px','%'),'',strtolower($colWidth3))); 
		for( $i=0; $i<$rows; $i++ ){ 
			$oddEven=(($i+1) % 2)?'odd':'even';
			$shortWidth=(($i+1) % 2)?$colWidth:$colWidth3;
			if(($i+1) % 2){ //odd
				$longWidth=$cumulativeWidth3-intval(str_replace(array('px','%'),'',strtolower($colWidth)));
				$longWidth.=(strstr($colWidth,'%'))?'%':'px';
			}else{ //even
				$longWidth=$cumulativeWidth3-intval(str_replace(array('px','%'),'',strtolower($colWidth3)));
				$longWidth.=(strstr($colWidth3,'%'))?'%':'px';
			}	
			echo '<div class="ccm-layout-row ccm-layout-row-'.$oddEven.'">';
				echo '<div class="ccm-layout-cell ccm-layout-cell-short" style="width:'.$shortWidth.'">';
				$a = new Area($this->getLayoutName().' Cell '.$this->getCellNumber());
				$a->display($c);			
				echo '</div>';
				echo '<div class="ccm-layout-cell ccm-layout-cell-long" style="width:'.$longWidth.'">';
				$a = new Area($this->getLayoutName().' Cell '.$this->getCellNumber());
				$a->display($c);			
				echo '</div>';	
				echo '<div class="ccm-spacer"></div>';			
			echo '</div>';
		}
		echo '</div>';	
	}
	*/
	
	protected $cellNum=0;
	protected function getCellNumber(){
		$this->cellNum++;
		return $this->cellNum;
	}
	protected function resetCellNumber(){ $this->cellNum=0; } 
	 
	 
	public function deleteCellsBlocks($c,$cellNumber=0){  
		$blocks = $c->getBlocks( $this->getCellAreaHandle(intval($cellNumber)) );
		foreach($blocks as $block) 
			$block->delete();
	}
	
	public function moveCellsBlocksToParent($c,$cellNumber=0){
		$blocks = $c->getBlocks(  );
		$i=1000;
		foreach($blocks as $block){  
			$i++;
			$db = Loader::db();
			$v = array( $this->getAreaHandle(), $i, $block->bID, $c->getCollectionID(), $c->getVersionID(), $this->getCellAreaHandle(intval($cellNumber)) );
			$db->Execute('update CollectionVersionBlocks set arHandle=?, cbDisplayOrder=? WHERE bID=? AND cID=? AND cvID=? AND arHandle=?', $v);
		}
	}
	
	static function cleanupOrphans(){
		$db = Loader::db();
		$sql = 'SELECT l.layoutID FROM Layouts AS l LEFT JOIN CollectionVersionAreaLayouts AS cval ON l.layoutID=cval.layoutID '. 
			   'LEFT JOIN LayoutPresets AS lp ON l.layoutID=lp.layoutID '.
			   'WHERE cval.layoutID IS NULL AND lp.lpID IS NULL';
		$layoutIds = $db->getCol( $sql );
		foreach($layoutIds as $layoutId){ 
			$db->query('DELETE FROM Layouts WHERE layoutID='.intval($layoutId));
		}
	}
 }
