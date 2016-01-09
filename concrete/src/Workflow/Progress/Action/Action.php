<?php
namespace Concrete\Core\Workflow\Progress\Action;
use \Concrete\Core\Foundation\Object;
class Action extends Object {

	protected $wrActionStyleClass = '';
	protected $wrActionStyleInnerButtonLeft = '';
	protected $wrActionStyleInnerButtonRight = '';
	protected $wrActionTask = '';
	protected $wrActionOnClick = '';
	protected $wrActionURL = '';
	protected $wrActionExtraButtonParameters = array();

	public function setWorkflowProgressActionStyleClass($class) {
		$this->wrActionStyleClass = $class;
	}
	public function setWorkflowProgressActionStyleInnerButtonLeftHTML($html) {
		$this->wrActionStyleInnerButtonLeft = $html;
	}
	public function setWorkflowProgressActionStyleInnerButtonRightHTML($html) {
		$this->wrActionStyleInnerButtonRight = $html;
	}
	public function setWorkflowProgressActionLabel($label) {
		$this->wrActionLabel = $label;
	}
	public function setWorkflowProgressActionTask($wrActionTask) {
		$this->wrActionTask = $wrActionTask;
	}
	public function setWorkflowProgressActionURL($wrActionURL) {
		$this->wrActionURL = $wrActionURL;
	}

	public function addWorkflowProgressActionButtonParameter($key, $value) {
		$this->wrActionExtraButtonParameters[$key] = $value;
	}

	public function getWorkflowProgressActionExtraButtonParameters() {
		return $this->wrActionExtraButtonParameters;
	}

	public function getWorkflowProgressActionStyleClass() {
		return $this->wrActionStyleClass;
	}
	public function getWorkflowProgressActionStyleInnerButtonLeftHTML() {
		return $this->wrActionStyleInnerButtonLeft;
	}
	public function getWorkflowProgressActionStyleInnerButtonRightHTML() {
		return $this->wrActionStyleInnerButtonRight;
	}
	public function getWorkflowProgressActionLabel() {
		return $this->wrActionLabel;
	}
	public function getWorkflowProgressActionTask() {
		return $this->wrActionTask;
	}
	public function getWorkflowProgressActionURL() {
		return $this->wrActionURL;
	}
}
