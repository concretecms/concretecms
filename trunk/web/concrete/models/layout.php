<? 

/**
 * @package Users
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 
 
 
 
 NOTES:
 
 add check to make sure edit interface is editing a layout for the area in question 
 
 fix problem with clicking on slider, and with delayed drag update 
 
 layout delete: what to do with lost blocks? provide popup option?  !!!!!!!!
 
 change search indexing to blacklist approach instead of whitelist approach  !!!!!!
 
 in process, when adding layout, check that this layout id has the correct area and collection, to prevent hacks
 
 when quicksaving, locking, or deleting a layout, make sure that layout belongs to that area  
 
 orphaned layout cleanup process? 
 
 make sure new tables have good indexes on them 
 
 fix dragging one slider over the next issue !!!!!!
 
 */
 
 
 
 class Layout extends Object {
	 
	public static $tableName='Layout';
	
	public $layoutID=0;
	//public $layoutName='Layout';
	public $type='table';
	public $columns=3;
	public $rows=3;	
	public $locked=0;	
	public $layoutTypes=array('area','table','columns','itemlist','staggered');
	public $breakpoints=array();
	public $areaNameNumber=0;
	
	
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
	public function getLayoutNameTxt(){ return t('Layout'); }
	public function getLayoutName(){ return $this->getAreaHandle().$this->getLayoutNameDivider().' '.$this->getLayoutNameTxt().' '.$this->getAreaNameNumber(); }
	
	public static function getById( $layoutID ){ 
		if(!intval($layoutID) ) return false; 
	 		
		//$cachedObj=self::retrieveFromRuntimeCache( $layoutID );
		//if( $cachedObj ) return $cachedObj; 
	
		$db = Loader::db();	
		$vals = array( intval($layoutID) );
		$sql = 'SELECT * FROM Layouts WHERE layoutID=?';
		$data = $db->getRow($sql,$vals); 
		if( !$data || !count($data) ) return false;  
		$layout = new Layout( $data ); 
		//$layout->addToRuntimeCache();
		
		return $layout;
	}
	
	//when editing a layout, it should be the only one tied to that collection version. Used in process->atask=layout->edit 
	public function isUniqueToCollectionVersion($c){
		$db = Loader::db();	
		$vals = array( intval($c->cID), $this->getLayoutID() );
		$sql = 'SELECT count(*) FROM CollectionVersionAreaLayouts WHERE cID=? AND layoutID=?'; 
		return ( intval($db->getOne($sql,$vals))==1) ? true:false; 
	}
	
	//breakpoints an optional array of percentages, for the break points between columns, 
	//for a three column layout, you could for instance set the column breaks like array('25%','75%')
	function fill( $params=array( 'layoutID'=>0, 'type'=>'table','rows'=>3,'columns'=>3, 'breakpoints'=>array(), 'locked'=>0 ) ){ 
	
		$this->layoutID=intval($params['layoutID']); 
		$this->locked=intval($params['locked']); 
		$this->type = (!in_array($params['type'],$this->layoutTypes))?'table':$params['type'];
		$this->rows=(intval($params['rows'])<1)?3:$params['rows']; 
		$this->columns=(intval($params['columns'])<1)?3:$params['columns']; 
		if(intval($params['areaNameNumber'])) $this->areaNameNumber = intval($params['areaNameNumber']);  
		
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
		$vals = array( intval($this->columns), intval($this->rows), intval($this->locked), join(',',$this->breakpoints)  );
		
		
		if( intval($this->layoutID) ){ 
			$sql = 'UPDATE Layouts SET columns=?, rows=?, locked=?, breakpoints=? WHERE layoutID=' . $this->getLayoutId() ; 
		}else{   
			$sql = 'INSERT INTO Layouts ( columns, rows, locked, breakpoints ) values (?, ?, ?, ?)'; 
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
		
		if( count($this->breakpoints)==0 || (count($this->breakpoints)!=($this->columns-1) && count($this->breakpoints)!=$this->columns) ){  
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
		
		if(!in_array($this->type,$this->layoutTypes)) $this->layoutType='table'; 
		
		echo '<div id="ccm-layout-wrapper-'.$this->layoutID.'" class="ccm-layout-wrapper">';
		
		if ($c->isEditMode()) { 
			$args = array('layout'=>$this);
			Loader::element('block_area_layout_controls', $args); 
		}
		
		$this->displayTableGrid($this->rows,$this->columns); 
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
		return $this->getLayoutName().$this->getLayoutNameDivider().' Cell '.intval($cellNumber); 
	}
	
	
	protected function displayTableGrid($rows=3,$columns=3,$c=NULL){
		if(!$c) global $c;
		if($c->isEditMode()) $editMode='ccm-edit-mode';
		if(!intval($rows)) $rows=3;
		if(!intval($columns)) $columns=3;
		$layoutNameClass = 'ccm-layout-name-'.TextHelper::camelcase($this->getAreaHandle()).'-'.TextHelper::camelcase($this->getLayoutNameTxt()).'-'.$this->getAreaNameNumber();
		echo '<div id="ccm-layout-'.$this->layoutID.'" class="ccm-layout ccm-layout-table  '.$layoutNameClass.' '.$editMode.'">';
		for( $i=0; $i<$rows; $i++ ){
			echo '<div class="ccm-layout-row ccm-layout-row-'.($i+1).'">';
				$cumulativeWidth=0;
				for( $j=0; $j<$columns; $j++ ){	 
					$colWidth=($columns==1)?'100%':$this->getNextColWidth($j,$cumulativeWidth);
					$cumulativeWidth += intval(str_replace(array('px','%'),'',strtolower($colWidth)));
					$columnn_id = 'ccm-layout-'.intval($this->layoutID).'-col-'.($j+1);
					echo '<div id="'.$columnn_id.'" class="ccm-layout-cell ccm-layout-col ccm-layout-col-'.($j+1).'" style="width:'.$colWidth.'">';
					$a = new Area( $this->getCellAreaHandle($this->getCellNumber()) );
					ob_start();
					$a->display($c);			
					$areaHTML = ob_get_contents();
					ob_end_clean();
					if(strlen($areaHTML)) echo $areaHTML;
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
	 
 }
 
 ?>