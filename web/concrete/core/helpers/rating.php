<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions for dealing with ratings. Input-specific functions are found in form/rating
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Rating {
	
	public function outputDisplay($value) {
		$html = '';
		$star1 = 'rating-star-off';
		$star2 = 'rating-star-off';
		$star3 = 'rating-star-off';
		$star4 = 'rating-star-off';
		$star5 = 'rating-star-off';
		
		if ($value > 4) {
			$star1 = 'rating-star-on-half';
		}
		if ($value > 14) {
			$star1 = 'rating-star-on';
		}
		if ($value > 24) {
			$star2 = 'rating-star-on-half';
		}
		if ($value > 34) {
			$star2 = 'rating-star-on';
		}
		if ($value > 44) {
			$star3 = 'rating-star-on-half';
		}
		if ($value > 54) {
			$star3 = 'rating-star-on';
		}
		if ($value > 64) {
			$star4 = 'rating-star-on-half';
		}
		if ($value > 74) {
			$star4 = 'rating-star-on';
		}
		if ($value > 84) {
			$star5 = 'rating-star-on-half';
		}
		if ($value > 94) {
			$star5 = 'rating-star-on';
		}		
		$html .= '<div class="ccm-rating">';
		$html .= '<div class="rating-star rating-star-readonly ' . $star1 . '"><a href="javascript:void(0)"></a></div>';		
		$html .= '<div class="rating-star rating-star-readonly ' . $star2 . '"><a href="javascript:void(0)"></a></div>';		
		$html .= '<div class="rating-star rating-star-readonly ' . $star3 . '"><a href="javascript:void(0)"></a></div>';		
		$html .= '<div class="rating-star rating-star-readonly ' . $star4 . '"><a href="javascript:void(0)"></a></div>';		
		$html .= '<div class="rating-star rating-star-readonly ' . $star5 . '"><a href="javascript:void(0)"></a></div>';		
		$html .= '</div>';
		return $html;
	}
	
	public function output($field, $value, $isEditableField = false, $includeJS = true) {
		if ($isEditableField == false) {
			return $this->outputDisplay($value);
		}
		
		$form = Loader::helper("form");
		$v = $form->getRequestValue($field);
		if ($v !== false) {
			$value = $v;
		}
		
		$html = '';
		$checked1 = ($value == 20) ? 'checked' : '';
		$checked2 = ($value == 40) ? 'checked' : '';
		$checked3 = ($value == 60) ? 'checked' : '';
		$checked4 = ($value == 80) ? 'checked' : '';
		$checked5 = ($value == 100) ? 'checked' : '';
		
		if ($isEditableField == false) {
			$disabled = 'disabled';
		}
		
		$html .= "<div class=\"ccm-rating\" id=\"ccm-rating-{$field}\">
			<input name=\"{$field}\" type=\"radio\" value=\"20\" {$checked1} {$disabled}/>
			<input name=\"{$field}\" type=\"radio\" value=\"40\" {$checked2} {$disabled}/>
			<input name=\"{$field}\" type=\"radio\" value=\"60\" {$checked3} {$disabled} />
			<input name=\"{$field}\" type=\"radio\" value=\"80\" {$checked4} {$disabled}/>
			<input name=\"{$field}\" type=\"radio\" value=\"100\" {$checked5} {$disabled}/>
		</div>";
		if ($includeJS) { 
			$html .= "<script type=\"text/javascript\">
				$(function() {
					$('input[name=\"{$field}\"]').rating();
				});
				</script>";
		}
		return $html;		
	}
	
	public function outputAverage($field, $value, $includeJS = true) {
		$html = '';
		$html = '';
		$checked1 = ($value == 10) ? 'checked' : '';
		$checked2 = ($value == 20) ? 'checked' : '';
		$checked3 = ($value == 30) ? 'checked' : '';
		$checked4 = ($value == 40) ? 'checked' : '';
		$checked5 = ($value == 50) ? 'checked' : '';
		$checked6 = ($value == 60) ? 'checked' : '';
		$checked7 = ($value == 70) ? 'checked' : '';
		$checked8 = ($value == 80) ? 'checked' : '';
		$checked9 = ($value == 90) ? 'checked' : '';
		$checked10 = ($value == 100) ? 'checked' : '';
		
		//if ($isEditableField == false) {
			$disabled = 'disabled';
		//}
		
		$html .= "<div class=\"ccm-rating\" id=\"ccm-rating-{$field}\">
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"10\" {$checked1} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"20\" {$checked2} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"30\" {$checked3} {$disabled} />
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"40\" {$checked4} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"50\" {$checked5} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"60\" {$checked6} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"70\" {$checked7} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"80\" {$checked8} {$disabled} />
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"90\" {$checked9} {$disabled}/>
			<input name=\"{$field}\" class=\"{split: 2}\" type=\"radio\" value=\"100\" {$checked10} {$disabled}/>
		</div>";
		
		if ($includeJS) { 
			$html .= "<script type=\"text/javascript\">
				$(function() {
					$('input[name=\"{$field}\"]').rating();
				});
				</script>";
		}
		return $html;		
	}
	
	
	
	public function getAverageChildRating($cItem, $akHandle = 'rating') {
		$cID = (is_object($cItem)) ? $cItem->getCollectionID() : $cItem;
		$db = Loader::db();
		Loader::model('attribute/categories/collection');
		$ak = CollectionAttributeKey::getByHandle($akHandle);
		if (is_object($ak)) {
			$val = $db->GetOne('select avg(ak_' . $akHandle . ') from CollectionSearchIndexAttributes c inner join Pages p on p.cID = c.cID where p.cParentID = ?', array($cID));
			return $val;
		}		
	}
	
	public function outputAverageChildRating($cItem, $akHandle = 'rating', $fieldOverride = false) {
		$rating = $this->getAverageChildRating($cItem, $akHandle);
		$rating = round($rating / 10) * 10;
		$field = ($fieldOverride) ? $fieldOverride : $akHandle;		
		print $this->outputAverage($field, $rating);
	}

	
	
}
