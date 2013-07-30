<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Blocks
 * @subpackage Date Archive
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */	
class Concrete5_Controller_Block_DateArchive extends BlockController {

	protected $btTable = 'btDateArchive';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "350";

	protected $btExportPageColumns = array('targetCID');
	protected $btCacheBlockRecord = true;
		
	public $helpers =  array('navigation');	
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	*/
	public function getBlockTypeDescription() {
		return t("Displays month archive for pages");
	}
	
	public function getBlockTypeName() {
		return t("Blog Date Archive");
	}

	public function getJavaScriptStrings() {
		return array(
			'num-months-missing' => t('Please enter the number of months you want to show.')
		);
	}
		
	public function view() {
		if($this->targetCID > 0) {
			$target = Page::getByID($this->targetCID);
			$this->set('target',$target);
		}		
		
		$query = "SELECT MIN(cv.cvDatePublic) as firstPost 
			FROM CollectionVersions cv inner join Pages on cv.cID = Pages.cID
			INNER JOIN PageTypes pt ON cv.ctID = pt.ctID
			WHERE pt.ctHandle IN ('blog_entry') and cIsTemplate = 0 and cvIsApproved = 1 and cIsActive = 1";
		$db = Loader::db();
		$firstPost = $db->getOne($query);

		if(strlen($firstPost)) {
			$firstPost = new DateTime($firstPost);
			$this->set('firstPost',$firstPost);
		}
	}
	
	public function save($args) {
		parent::save($args);
	}	
}

if(!function_exists('date_diff')) {
    class DateInterval {
        public $y;
        public $m;
        public $d;
        public $h;
        public $i;
        public $s;
        public $invert;
       
        public function format($format) {
            $format = str_replace('%R%y', ($this->invert ? '-' : '+') . $this->y, $format);
            $format = str_replace('%R%m', ($this->invert ? '-' : '+') . $this->m, $format);
            $format = str_replace('%R%d', ($this->invert ? '-' : '+') . $this->d, $format);
            $format = str_replace('%R%h', ($this->invert ? '-' : '+') . $this->h, $format);
            $format = str_replace('%R%i', ($this->invert ? '-' : '+') . $this->i, $format);
            $format = str_replace('%R%s', ($this->invert ? '-' : '+') . $this->s, $format);
           
            $format = str_replace('%y', $this->y, $format);
            $format = str_replace('%m', $this->m, $format);
            $format = str_replace('%d', $this->d, $format);
            $format = str_replace('%h', $this->h, $format);
            $format = str_replace('%i', $this->i, $format);
            $format = str_replace('%s', $this->s, $format);
           
            return $format;
        }
    }

    function date_diff(DateTime $date1, DateTime $date2) {
        $diff = new DateInterval();
        if($date1 > $date2) {
            $tmp = $date1;
            $date1 = $date2;
            $date2 = $tmp;
            $diff->invert = true;
        }
       
        $diff->y = ((int) $date2->format('Y')) - ((int) $date1->format('Y'));
        $diff->m = ((int) $date2->format('n')) - ((int) $date1->format('n'));
        if($diff->m < 0) {
            $diff->y -= 1;
            $diff->m = $diff->m + 12;
        }
        $diff->d = ((int) $date2->format('j')) - ((int) $date1->format('j'));
        if($diff->d < 0) {
            $diff->m -= 1;
            $diff->d = $diff->d + ((int) $date1->format('t'));
        }
        $diff->h = ((int) $date2->format('G')) - ((int) $date1->format('G'));
        if($diff->h < 0) {
            $diff->d -= 1;
            $diff->h = $diff->h + 24;
        }
        $diff->i = ((int) $date2->format('i')) - ((int) $date1->format('i'));
        if($diff->i < 0) {
            $diff->h -= 1;
            $diff->i = $diff->i + 60;
        }
        $diff->s = ((int) $date2->format('s')) - ((int) $date1->format('s'));
        if($diff->s < 0) {
            $diff->i -= 1;
            $diff->s = $diff->s + 60;
        }
       
        return $diff;
    }
}
