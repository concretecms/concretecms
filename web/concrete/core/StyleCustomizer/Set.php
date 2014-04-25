<?php
namespace Concrete\Core\StyleCustomizer;
class Set {

	protected $name;
	protected $elements = array();

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function addStyle(\Concrete\Core\StyleCustomizer\Style\Style $style) {
		$this->styles[] = $style;
	}

	public function getStyles() {
		return $this->styles;
	}


}