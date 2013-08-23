<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Displays a search prompt and results.
 *
 * @package Blocks
 * @subpackage Search
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Controller_Block_Search extends BlockController {

	protected $btTable = 'btSearch';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "240";
	protected $btWrapperClass = 'ccm-ui';

	public $title = "";
	public $buttonText = ">";
	public $baseSearchPath = "";
	public $resultsURL = "";
	public $postTo_cID = "";

	protected $hColor = '#EFE795';

	public function highlightedMarkup($fulltext, $highlight) {
		if (!$highlight) {
			return $fulltext;
		}

		$this->hText = $fulltext;
		$this->hHighlight  = str_replace(array('"',"'","&quot;"),'',$highlight); // strip the quotes as they mess the regex
		$this->hText = @preg_replace( "#$this->hHighlight#ui", '<span style="background-color:'. $this->hColor .';">$0</span>', $this->hText );
		return $this->hText;
	}
	
	public function validate($post) {
		$errors = Loader::helper('validation/error');
		if ($post['title'] === false || $post['title'] == '') {
			$errors->add(t("Please enter your Search Title."));
		}
		if ($post['buttonText'] === false || $post['buttonText'] == '') {
			$errors->add(t("Please enter your Submit Button Text."));
		}
		
		return $errors;
	}
	
	public function highlightedExtendedMarkup($fulltext, $highlight) {
		$text = @preg_replace("#\n|\r#", ' ', $fulltext);

		$matches = array();
		$highlight = str_replace(array('"',"'","&quot;"),'',$highlight); // strip the quotes as they mess the regex

		if (!$highlight) {
			$text = Loader::helper('text')->shorten($fulltext, 180);
			if (strlen($fulltext) > 180) {
				$text . '&hellip;<wbr>';
			}
			return $text;
		}

		$regex = '([[:alnum:]|\'|\.|_|\s]{0,45})'. preg_quote($highlight, '#') .'([[:alnum:]|\.|_|\s]{0,45})';
		preg_match_all("#$regex#ui", $text, $matches);

		if(!empty($matches[0])) {
			$body_length = 0;
			$body_string = array();
			foreach($matches[0] as $line) {
				$body_length += strlen($line);

				$r = $this->highlightedMarkup($line, $highlight);
				if ($r) {
					$body_string[] = $r;
				}
				if($body_length > 150)
					break;
			}
			if(!empty($body_string))
				return @implode("&hellip;<wbr>", $body_string);
		}
	}

	public function setHighlightColor($color) {
		$this->hColor = $color;
	}
	
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
		$db = Loader::db();
		$numRows = $db->GetOne('select count(cID) from PageSearchIndex');
		return ($numRows > 0);
	}

	function view(){
		$c = Page::getCurrentPage();
		$this->set('title', $this->title);
		$this->set('buttonText', $this->buttonText);
		$this->set('baseSearchPath', $this->baseSearchPath);
		$this->set('postTo_cID', $this->postTo_cID);

		$resultsURL = $c->getCollectionPath();
		
		if ($this->resultsURL != '') {
			$resultsURL = $this->resultsURL;
		} else if ($this->postTo_cID != '') {
			$resultsPage = Page::getById($this->postTo_cID);
			$resultsURL = $resultsPage->cPath;
		}

		$this->set('resultTargetURL', $resultsURL);

		//run query if display results elsewhere not set, or the cID of this page is set
		if( !empty($_REQUEST['query']) || isset($_REQUEST['akID']) || isset($_REQUEST['month']))  {
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

		if( intval($data['postTo_cID'])>0 ){
			$args['postTo_cID'] = intval($data['postTo_cID']);
		} else {
			$args['postTo_cID'] = '';
		}

		$args['resultsURL'] = ( $data['externalTarget']==1 && strlen($data['resultsURL'])>0 ) ? trim($data['resultsURL']) : '';
		parent::save($args);
	}

	public $reservedParams=array('page=','query=','search_paths[]=','submit=','search_paths%5B%5D=' );

	function do_search() {
		$q = $_REQUEST['query'];
		// i have NO idea why we added this in rev 2000. I think I was being stupid. - andrew
		// $_q = trim(preg_replace('/[^A-Za-z0-9\s\']/i', ' ', $_REQUEST['query']));
		$_q = $q;
		Loader::library('database_indexed_search');
		$ipl = new IndexedPageList();
		$aksearch = false;
		$ipl->ignoreAliases();
		if (is_array($_REQUEST['akID'])) {
			Loader::model('attribute/categories/collection');
			foreach($_REQUEST['akID'] as $akID => $req) {
				$fak = CollectionAttributeKey::getByID($akID);
				if (is_object($fak)) {
					$type = $fak->getAttributeType();
					$cnt = $type->getController();
					$cnt->setAttributeKey($fak);
					$cnt->searchForm($ipl);
					$aksearch = true;
				}
			}
		}

		if (isset($_REQUEST['month']) && isset($_REQUEST['year'])) {
			$month = strtotime($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-01');
			$month = date('Y-m-', $month);
			$ipl->filterByPublicDate($month . '%', 'like');
			$aksearch = true;
		}


		if (empty($_REQUEST['query']) && $aksearch == false) {
			return false;
		}

		$ipl->setSimpleIndexMode(true);
		if (isset($_REQUEST['query'])) {
			$ipl->filterByKeywords($_q);
		}

		if( is_array($_REQUEST['search_paths']) ){
			foreach($_REQUEST['search_paths'] as $path) {
				if(!strlen($path)) continue;
				$ipl->filterByPath($path);
			}
		} else if ($this->baseSearchPath != '') {
			$ipl->filterByPath($this->baseSearchPath);
		}

		$ipl->filter(false, '(ak_exclude_search_index = 0 or ak_exclude_search_index is null)');

		$res = $ipl->getPage();

		foreach($res as $r) {
			$results[] = new IndexedSearchResult($r['cID'], $r['cName'], $r['cDescription'], $r['score'], $r['cPath'], $r['content']);
		}

		$this->set('query', $q);
		$this->set('paginator', $ipl->getPagination());
		$this->set('results', $results);
		$this->set('do_search', true);
		$this->set('searchList', $ipl);
	}

}
