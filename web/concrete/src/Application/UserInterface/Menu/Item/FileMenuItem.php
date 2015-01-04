<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

class FileMenuItem extends Item
{
	const ACTION_AJAX_REQUEST = 'ajax';	  	//!< @javascript-exported
	const ACTION_OPEN_DIALOG  = 'dialog'; 	//!< @javascript-exported
	const ACTION_DOWNLOAD     = 'download';	//!< @javascript-exported
	const ACTION_OPEN		  = 'open';		//!< @javascript-exported
	const ACTION_SEPARATOR	  = 'separator';//!< @javascript-exported

	protected $handleMultiple = true;

	public function handleMultiple() { return $this->handleMultiple; }
	public function setHandleMultiple( $handleMultiple = true ) { $this->handleMultiple = $handleMultiple; }

	protected $actionType = self::ACTION_OPEN_DIALOG;

	public function getActionType() { return $this->actionType; }
	public function setActionType($type) 
	{ 
		$this->setLinkAttribute( 'data-filemenu-type', $type );
		$this->actionType = $type; 
	}

	public function isSeparator() { return self::ACTION_SEPARATOR == $this->actionType; }


	public function setLinkAttributes( $attributes )
	{
		parent::setLinkAttributes( $attributes );

		$this->setLinkAttribute( 'data-filemenu-type', $type );
	}

}
