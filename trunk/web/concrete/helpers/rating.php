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

defined('C5_EXECUTE') or die(_("Access Denied."));
class RatingHelper {
	
	public function output($field, $value, $isEditableField = false) {
		$html = '';
		$html = '';
		$checked1 = ($value == 20) ? 'checked' : '';
		$checked2 = ($value == 40) ? 'checked' : '';
		$checked3 = ($value == 60) ? 'checked' : '';
		$checked4 = ($value == 80) ? 'checked' : '';
		$checked5 = ($value == 100) ? 'checked' : '';
		
		if ($isEditableField == false) {
			$disabled = 'disabled';
		}
		
		$html .=<<<EOS
		<div class="ccm-rating" id="ccm-rating-{$field}">
			<input name="{$field}" type="radio" value="20" {$checked1} {$disabled}/>
			<input name="{$field}" type="radio" value="40" {$checked2} {$disabled}/>
			<input name="{$field}" type="radio" value="60" {$checked3} {$disabled} />
			<input name="{$field}" type="radio" value="80" {$checked4} {$disabled}/>
			<input name="{$field}" type="radio" value="100" {$checked5} {$disabled}/>
		</div>
		<div class="ccm-spacer">&nbsp;</div>
		<script type="text/javascript">
		$(function() {
			$('input[name={$field}]').rating();
		});
		</script>
EOS;
			
		return $html;

		
		return $html;		
	}

	
	
}