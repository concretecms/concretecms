/** 
 * concrete5 in context editing
 */

var CCMEditMode = function() {

	return {
		start: function() {
			$('.ccm-area').ccmmenu();
			$('.ccm-block-edit').ccmmenu();
			$('.ccm-block-edit-layout').ccmmenu();
		}
	}

}();