<?

/**
 * Helper class for finding spelling mistakes in text.  Requires the linux package aspell.
 * @package Helpers
 * @category Concrete 
 * @subpackage Validation
 * @author Tony Trupp <tony@concretecms.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

class Concrete5_Helper_Spellchecker {

	public $wordSuggestions=array();
	public $aspell_path = DIR_FILES_BIN_ASPELL;

	//check to see if aspell is installed - returns a boolean
	public function enabled(){
		$mistakes = `echo "norzbibbit flagnbaggel koolalalooper" | {$this->aspell_path} list`;
		return (strlen($mistakes)>0)?1:0;
	}
	
	// sets the location of aspell if it's not in your path 
	public function setAspellPath($path) {
		$this->aspell_path = $path;
	} 
	
	/** 
	 * Returns a link to the js library, as well as actual strings that can be used
	 */
	public function init() {
		print '<script src="' . ASSETS_URL_JAVASCRIPT . '/ccm.spellchecker.js"></script>';
		print '<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.spellchecker.css";</style>';
	}

	public function findMisspellings($string){  
		  
		$pre='<span id="misspelledWordWORD_NUMBER" class="misspelled">'; // Inserted before each mis-spelling.
		$post='</span>'; // Inserted after each mis-spelling.
		//$string=strtr($string,"\n"," ");
		$string=str_replace("\r"," cartographic ",$string);
		$string=str_replace("\n"," cartographic ",$string);
		//return htmlentities($string);		
		// Drop newlines in string. (It bothers aspell in this context.)
		$cleanStr =	preg_replace("/[^a-z0-9\'\-]/i",' ', $string);		
		$cleanStr =	addslashes( $cleanStr );
		
		$mistakes = `echo "$cleanStr" | {$this->aspell_path} list`;
		// Get list or errors.
		$offset=0; 
		
		$htmlentitiesStr=htmlentities($string, ENT_QUOTES, APP_CHARSET); 
		$wordNumber=0;
		foreach (explode("\n",$mistakes) as $word){
			$wordNumber++;
			// Walk list, inserting $pre and $post strings.  I move along and
			// do the insertions, keeping track of the location.  A global replace
			// with str_replace($string,$pre.$work.$post) is problematic if the
			// same misspelling is found more than once.
			$this->wordSuggestions['word'.$wordNumber]=$this->makeSuggestions($word); 
			
			//$string.=' ('.$this->wordSuggestions[$word].') ';
			if ($word<>"") {
				$uniquePre=str_replace('WORD_NUMBER',$wordNumber,$pre);
				$offset=strpos($htmlentitiesStr,$word,$offset);
				$htmlentitiesStr=substr_replace($htmlentitiesStr, $post, $offset+strlen($word), 0);
				$htmlentitiesStr=substr_replace($htmlentitiesStr, $uniquePre, $offset, 0);
				$offset=$offset+strlen($word)+strlen("$uniquePre $post");   
			};
		}
		return str_replace(' cartographic ',' <br>',$htmlentitiesStr);
	}
	
	public function makeSuggestions($word){
		$suggestionTxt = `echo $word | {$this->aspell_path} -a`;
		$suggestionTxtLines=explode("\n",$suggestionTxt);
		foreach($suggestionTxtLines as $suggestionTxtLine){
			if( substr($suggestionTxtLine,0,1)=='&' ){
				$pos=strpos($suggestionTxtLine,':')+1;
				return substr($suggestionTxtLine,$pos);
			}
		}
		return '';
	}
	
	public function getSuggestionPairsJSON(){
		$suggestionPairs=array();
		foreach($this->wordSuggestions as $key=>$suggestionsStr){
			if(strlen(trim($key))==0) continue;
			$suggestionPair = $key.':';
			$suggestionsArray=explode(',',$suggestionsStr);
			$fixedSuggestionsArray=array();
			foreach($suggestionsArray as $suggestion){
				$fixedSuggestionsArray[]='"'.addslashes(trim($suggestion)).'"';
			}
			$suggestionPair .= '['.join(', ',$fixedSuggestionsArray).']';
			$suggestionPairs[]=$suggestionPair;
		}
		return $suggestionPairs;
	}
}

?>