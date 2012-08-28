<?
/**
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Special form elements for rating an item.
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Form_Rating {

	
	/** 
	 * Creates form fields and JavaScript rating includes for a particular item
	 * <code>
	 *     $dh->datetime('yourStartDate', '2008-07-12 3:00:00');
	 * </code>
	 * @param string $prefix
	 * @param string $value
	 * @param bool $includeActivation
	 * @param bool $calendarAutoStart
	 */
	 
	// This is the combined view that shows an aggregate value, and lets you pick
	/*
	public function rating($prefix, $value = null) {
		$field = $prefix;
		$html = '';
		$html .=<<<EOS
		<div class="ccm-rating-display" id="ccm-rating-display-{$prefix}" style="display: block">
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="0.5"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="1.0"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="1.5"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="2.0"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="2.5"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="3.0"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="3.5" checked="checked"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="4.0"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="4.5"/>
			<input name="{$field}-display" class="star {split:2}" disabled="true" type="radio" value="5.0"/>
		</div>
		<div class="ccm-rating-active" id="ccm-rating-active-{$prefix}" style="display: none">
			<input name="{$field}" type="radio" value="1.0"/>
			<input name="{$field}" type="radio" value="2.0"/>
			<input name="{$field}" type="radio" value="3.0"/>
			<input name="{$field}" type="radio" value="4.0"/>
			<input name="{$field}" type="radio" value="5.0"/>
		</div>
		<div class="ccm-spacer">&nbsp;</div>
		<script type="text/javascript">
		$(function() {
			$('input[name={$field}]').rating();
			$('input[name={$field}-display]').rating();
			$('.ccm-rating-display').hover(function() {
				$('.ccm-rating-display').hide();
				$('.ccm-rating-active').show();
			}, function() {
			
			});
		});
		</script>
EOS;
			
		return $html;
	}
	
	*/
	
	public function rating($prefix, $value = null, $includeJS = true) {
		$rt = Loader::helper('rating');
		return $rt->output($prefix, $value, true, $includeJS);
	}
	
	
}