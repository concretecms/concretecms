<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class SearchBlockController extends BlockController {
	
	var $pobj;
	  
	protected $btTable = 'btSearch';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "170";
	
	public $title = "";
	public $buttonText = ">"; 
	public $baseSearchPath = "";
	public $resultsURL = "";
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Add a search box to your site.");
	}
	
	public function getBlockTypeName() {
		return t("Search");
	}		
	
	public function getJavaScriptStrings() {
		return array('search-title' => t('Please enter a valid search title.'));
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj);
		if ($this->title == '') {
			$this->title=t("Search");
		}
	}
	
	public function indexExists() {
		Loader::library('indexed_search');				
		Loader::library('3rdparty/Zend/Search/Lucene');
		try {
			$index = Zend_Search_Lucene::open(DIR_FILES_CACHE_PAGES);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}
	
	function view(){
		global $c; 
		$this->set('title', $this->title);
		$this->set('buttonText', $this->buttonText);
		$this->set('baseSearchPath', $this->baseSearchPath);			
		
		//auto target is the form action that is used if none is explicity set by the user
		$autoTarget=str_replace('query='.$_REQUEST['query'],'',$c->getCollectionPath());
		if( is_array($_REQUEST['search_paths']) ){
			foreach($_REQUEST['search_paths'] as $search_path){
				$autoTarget=str_replace('search_paths[]='.$search_path,'',$autoTarget);
				$autoTarget=str_replace('search_paths%5B%5D='.$search_path,'',$autoTarget);
			}
		}
		$autoTarget=str_replace('page='.$_REQUEST['page'],'',$autoTarget);
		$autoTarget=str_replace('submit='.$_REQUEST['submit'],'',$autoTarget);
		$autoTarget=str_replace(array('&&&&','&&&','&&'),'',$autoTarget);
		$resultTargetURL = (strlen($this->resultsURL)) ? $this->resultsURL : $autoTarget;			
		$this->set('resultTargetURL', $resultTargetURL);

		//run query if display results elsewhere not set, or the cID of this page is set
		if( strlen($_REQUEST['query']) && (strlen(trim($this->resultsURL))==0 || strstr($this->resultsURL,'cID='.$c->getCollectionId()) ) ){ 
			$this->do_search();
		}						
	}
	
	function save($data) { 
		$args['title'] = isset($data['title']) ? $data['title'] : '';
		$args['buttonText'] = isset($data['buttonText']) ? $data['buttonText'] : '';
		$args['baseSearchPath'] = isset($data['baseSearchPath']) ? $data['baseSearchPath'] : '';
		if( $args['baseSearchPath']=='OTHER' && intval($data['searchUnderCID'])>0 ){
			$customPathC = Page::getByID( intval($data['searchUnderCID']) );
			if( !$customPathC )	$args['baseSearchPath']='';
			else $args['baseSearchPath'] = $customPathC->getCollectionPath();
		}
		if( trim($args['baseSearchPath'])=='/' || strlen(trim($args['baseSearchPath']))==0 )
			$args['baseSearchPath']='';	
		$args['resultsURL'] = ( $data['externalTarget']==1 && strlen($data['resultsURL'])>0 ) ? trim($data['resultsURL']) : '';		
		parent::save($args);
	}
	
	public $reservedParams=array('page=','query=','search_paths[]=','submit=','search_paths%5B%5D=' );
	
	function do_search() {
		
		try {
		
			$q = $_REQUEST['query'];
			$this->search_paths=$_REQUEST['search_paths'];
			if( !is_array($this->search_paths) && strlen($this->search_paths)>0 ) 
				 $this->search_paths=array($this->search_paths);
			if( !is_array($this->search_paths) ) $this->search_paths=array();
			$pagination = Loader::helper('pagination');	
			
			if ($q != null) {
				Loader::library('indexed_search');				
				Loader::library('3rdparty/Zend/Search/Lucene');
				//Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
				Loader::library('3rdparty/Zend/Search/Lucene');
				Loader::library('3rdparty/StandardAnalyzer/Analyzer/Standard/English');
				Zend_Search_Lucene_Analysis_Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
				
				//search a path
				$subqueries = array();				
				if( count($this->search_paths) ){
					$pathsBooleanQuery = new Zend_Search_Lucene_Search_Query_Boolean();
					foreach($this->search_paths as $path){
						$pattern = new Zend_Search_Lucene_Index_Term($path, 'cPath');
						$pathsQuery = new Zend_Search_Lucene_Search_Query_Term($pattern);
						$pathsBooleanQuery->addSubquery($pathsQuery, NULL);
					}
					$subqueries[]=array('query'=>$pathsBooleanQuery,'required'=>true);
				}
				
				$results = IndexedSearch::search( $q, $subqueries );
				
				//pagination
				$pageSize=10;
				$page=intval($_REQUEST['page']);
				global $c;
				$cID=$c->getCollectionId();
				$cPath=$c->getCollectionPath();
				
				//clean and build query string from current URI
				$url=$_SERVER['REQUEST_URI'];
				if( !strstr($url,'?')) $url.='?';
				else{
					//strip non reserved params from query string, leave the unique params
					$qStr=substr($url,strpos($url,'?')+1);
					$qStrParts=explode('&',$qStr);
					$nonReservedQStrParts=array();
					foreach($qStrParts as $qStrPart){
						$reserved=0;
						foreach($this->reservedParams as $reservedParam){
							if( strstr($qStrPart,$reservedParam) ){
								$reserved=1;
								break;
							}
						}
						if($reserved) continue;
						$nonReservedQStrParts[]=$qStrPart;
					}
					$php_self=( !strstr($_SERVER['PHP_SELF'],'?') )?$_SERVER['PHP_SELF'].'?':$_SERVER['PHP_SELF'];
					$url=$php_self.join('&',$nonReservedQStrParts);
				}
				$pageBase=$url;
				
				$queryString='&page=%pageNum%&query=' . $q . '&search_paths%5B%5D='.join('&search_paths%5B%5D=',$this->search_paths);			
				$pagination->init($page,count($results),$pageBase.$queryString,$pageSize );	
				$limitedResults=$pagination->limitResultsToPage($results);
				
				$this->set('results', $limitedResults);				
			}			
					
			$this->set('query', htmlentities($q));
			$this->set('paginator', $pagination);
		
		} catch(Zend_Search_Lucene_Exception $e) {
			$this->set('error', t('Unable to complete search: ') . $e->getMessage());
		}
	}		
	
}

?>