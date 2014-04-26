<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Core;
class FontStyle extends Style {

	public function render() {
		$fh = Core::make('helper/form/font');
		print $fh->output($this->getVariable(), '', array());
	}


}