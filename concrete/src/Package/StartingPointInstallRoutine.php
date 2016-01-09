<?php

namespace Concrete\Core\Package;
class StartingPointInstallRoutine {

	public function __construct($method, $progress, $text = '') {
		$this->method = $method;
		$this->progress = $progress;
		$this->text = $text;
	}

	public function getMethod() {
		return $this->method;
	}

	public function getText() {
		return $this->text;
	}

	public function getProgress() {
		return $this->progress;
	}

}
