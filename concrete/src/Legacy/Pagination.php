<?php
namespace Concrete\Core\Legacy;
use stdClass;

class Pagination {


	public $current_page=0;	//Zero Based
	public $page_size=20;
	public $result_offset=0;
	public $number_of_pages=0;
	public $result_count=0;
	public $result_lower=0; //for 'Results lower-upper of result_count'
	public $result_upper=0;
	public $classOff='ltgray';
	public $classOn='';
	public $classCurrent='currentPage active';
	public $URL=''; //%pageNum% for page number
	public $jsFunctionCall='';
	public $queryStringPagingVariable;
	public $additionalVars = array();

    public function __construct() {
        $this->queryStringPagingVariable = \Config::get('concrete.seo.paging_string');
    }

	public function reset() {
		$this->current_page=0;	//Zero Based
		$this->page_size=20;
		$this->result_offset=0;
		$this->number_of_pages=0;
		$this->result_count=0;
		$this->result_lower=0; //for 'Results lower-upper of result_count'
		$this->result_upper=0;
		$this->classOff='ltgray';
		$this->classOn='';
		$this->classCurrent='currentPage active';
		$this->URL	='';
		$this->jsFunctionCall='';
		$this->queryStringPagingVariable = \Config::get('concrete.seo.paging_string');
		$this->additionalVars = array();
	}

	public function init($page_num,$num_results=0,$URL='',$size=20,$jsFunctionCall=''){
		$page_num=intval($page_num);
		if($page_num>0) $page_num--;
		$this->current_page=$page_num;
		$this->result_count=intval($num_results);
		if ($URL == false || $URL == '') {
			$this->URL = $this->getBaseURL();
		} else {
			$this->URL = $this->getBaseURL($URL);
		}
		$this->page_size=intval($size);
		//calulate the number of pages
		if ($this->page_size==0) $this->page_size=1;
		$this->number_of_pages=ceil($this->result_count/$this->page_size);
		//calulate the offset
		$this->result_offset=($this->current_page)*$this->page_size;
		$this->recalc($num_results);
		if($jsFunctionCall) $this->jsFunctionCall=$jsFunctionCall;
	}

	protected function getBaseURL($url = false) {
		$uh = Loader::helper('url');
		$args = array($this->queryStringPagingVariable => '%pageNum%');
		if (count($this->additionalVars) > 0) {
			foreach($this->additionalVars as $k => $v) {
				$args[$k] = $v;
			}
		}
		$url = $uh->setVariable($args, false, $url);
		return $url;
	}

	public function setAdditionalQueryStringVariables($args) {
		$this->additionalVars = $args;
	}

	public function recalc($num_results){
		//recalulate the number of pages
		$this->result_count=intval($num_results);
		if ($this->page_size==0) $this->page_size=1;
		$this->number_of_pages=ceil($this->result_count/$this->page_size);
		//set lower and upper bounds of this page
		if($this->result_count==0){
			$this->result_lower=0;
			$this->result_upper=0;
		}else{
			$this->result_lower=$this->result_offset+1;
			$this->result_upper=$this->result_offset+$this->page_size;
			//on last page, upper limit is not full page
			if(($this->current_page+1)==$this->number_of_pages)
				$this->result_upper=$this->result_count;
				//$this->result_upper=$this->result_offset+($this->result_count%$this->page_size);
		}
	}

	public function getLIMIT(){
		//for MYSQL Limit statement
		return $this->result_offset.','.$this->page_size;
	}

	public function getCurrentURL(){
		return str_replace("%pageNum%",$this->current_page, $this->URL);
	}

	public function getCurrentPage() {
		return $this->current_page;
	}

	public function getRequestedPage() {
		if (isset($_REQUEST[$this->queryStringPagingVariable])) {
			return intval($_REQUEST[$this->queryStringPagingVariable]);
		} else {
			return 1;
		}
	}

	public function hasNextPage() {
		if($this->number_of_pages==1) return;
		return $this->current_page < ($this->number_of_pages-1);
	}

	public function hasPreviousPage() {
		if($this->number_of_pages==1) return false;
		if ($this->current_page=="0") {
			return false;
		}
		return true;
	}

	public function getTotalPages() {
		return $this->number_of_pages;
	}

	public function getNextURL() {
		if ($this->hasNextPage()) {
			$linkURL = str_replace("%pageNum%", $this->getNextInt()+1, $this->URL);
			return $linkURL;
		}
	}

	public function getPreviousURL() {
		if ($this->hasPreviousPage()) {
			$linkURL = str_replace("%pageNum%", $this->getPreviousInt()+1, $this->URL);
			return $linkURL;
		}
	}

	function getNext($linkText = false, $wrapper = 'span'){
		if (!$linkText) {
			$linkText = t('Next') . ' &raquo;';
		}
		if($this->number_of_pages==1) return;
		//if not last page
		if (!$this->hasNextPage())
			 return '<' . $wrapper . ' class="'.$this->classOff.'"' . ($wrapper == 'a' ? ' href="#"':'') . '>'.$linkText.'</' . $wrapper . '>';
		 else{
			$linkURL=str_replace("%pageNum%", $this->getNextInt()+1, $this->URL);
			return '<a class="'.$this->classOn.'" href="'.$linkURL.'" '.$this->getJSFunctionCall($this->getNextInt()+1).'>'.$linkText.'</a>';
		}
	}

	public function getNextInt(){
		if ($this->current_page>=($this->number_of_pages-1)) return $this->number_of_pages-1;
		return $this->current_page+1;
	}

	public function getPrevious($linkText = false, $wrapper = 'span'){
		if (!$linkText) {
			$linkText = '&laquo; ' . t('Previous');
		}
		if($this->number_of_pages==1) return;
		//if not first page
		if ($this->current_page=="0")
			 return '<' . $wrapper . ' class="'.$this->classOff.'"' . ($wrapper == 'a' ? ' href="#"':'') . '>'.$linkText.'</' . $wrapper . '>';
		else{
			$linkURL=str_replace("%pageNum%", $this->getPreviousInt()+1, $this->URL);
			return '<a class="'.$this->classOn.'" href="'.$linkURL.'" '.$this->getJSFunctionCall($this->getPreviousInt()+1).'>'.$linkText.'</a>';
		}
	}

	public function getPreviousInt(){
		if(($this->current_page-1)<=0) return 0;
		return $this->current_page-1;
	}

	public function getAsJSONObject() {
		$pagination = new stdClass;
		$prevClass = 'prev';
		$nextClass = 'next';
		if (!$this->hasPreviousPage()) {
			$prevClass = 'prev disabled';
		}
		if (!$this->hasNextPage()) {
			$nextClass = 'next disabled';
		}
		$pagination->prevClass = $prevClass;
		$pagination->nextClass = $nextClass;
		$pagination->previousPage = $this->getPrevious();
		$pagination->nextPage = $this->getNext();
		$pagination->pages = $this->getPages('li');
		return $pagination;
	}

	function getPages($wrapper='span'){
		if($this->number_of_pages==1) return;
		$pages_made=0;
        $pages = '';
        $predotted = 0;
        $postdotted = 0;
		for ($i=0;$i<$this->number_of_pages;$i++){
			//preceeding dots for high number of pages
			if($i<($this->current_page-5) && $i!=0){
				if($predotted!=1){

					if($wrapper == 'li'){
						$pages.='<li class="ccm-pagination-ellipses disabled"><a href="#">...</a></li>';
					} else {
						$pages.='<span class="ccm-pagination-ellipses">...</span>';
					}

				   $predotted=1;

				}
				continue;
			}
			//following dots for high number of pages
			if($i>($this->current_page+5) && $i!=($this->number_of_pages-1)){
				if($postdotted!=1){

				   if($wrapper == 'li'){
						$pages.='<li class="ccm-pagination-ellipses disabled"><a href="#">...</a></li>';
					} else {
						$pages.='<span class="ccm-pagination-ellipses">...</span>';
					}

				   $postdotted=1;
				}
				continue;
			}

			//if not current page
			if ($this->current_page==$i){

					if($wrapper == 'li'){
						$pages.="<li class=\"{$this->classCurrent} numbers disabled\"><a href=\"#\">".($i+1)."</a></li>";
					} else {
						$pages.="<span class=\"{$this->classCurrent} numbers\"><strong>".($i+1)."</strong></span>";
					}

			} else {

				   $linkURL=str_replace("%pageNum%", $i+1, $this->URL);

					if($wrapper == 'li'){
						$pages.="<li class=\"{$this->classOn} numbers\"><a href=\"{$linkURL}\" ".$this->getJSFunctionCall($i+1).">".($i+1)."</a></li>";
					} else {
						$pages.="<span class=\"{$this->classOn} numbers\"><a href=\"{$linkURL}\" ".$this->getJSFunctionCall($i+1).">".($i+1)."</a></span>";
					}

			} //end if not current page
			$pages_made++;
		}
		return $pages;
	}

	//for turning an array into a pagable set (for use with lucene, rss, etc)
	public function limitResultsToPage( $results=array() ){
		$posIndexedResults=array();
		foreach( $results as $ignoredKey=>$result )
			$posIndexedResults[]=$result;
		$limitedResults=array();
		$start=$this->result_offset;
		$end=$this->result_offset+$this->page_size;
		//echo 	$start.' '.	$end.'<Br>';
		for ($pos = $start; $pos < $end; $pos++) {
			if(!$posIndexedResults[$pos]) continue;
			$limitedResults[]=$posIndexedResults[$pos];
		}
		return $limitedResults;
	}

	public function getJSFunctionCall($pageNum){
		if(!$this->jsFunctionCall) return '';
		return ' onclick="return '.$this->jsFunctionCall.'(this,'.$pageNum.');"';
	}
}
