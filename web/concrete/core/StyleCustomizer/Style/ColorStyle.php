<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Core;
class ColorStyle extends Style {

	public function render() {
		$fh = Core::make('helper/form/color');
		print $fh->output($this->getVariable(), '#f00');
	}


}