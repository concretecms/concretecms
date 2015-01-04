<?php
namespace Concrete\Core\Application\Service\UserInterface;

use Concrete\Core\Application\UserInterface\Menu\Item\FileMenuItem;
use Concrete\Core\Application\UserInterface\Menu\Item\Controller as ItemController;

class FileManagerMenu
{
	protected $menu = array();

	protected $bulkMenu = null;

	public function __construct()
	{
		$uh = \Core::make('helper/concrete/urls');

        $default = array (
            "ccm_download"   => array( "type" => FileMenuItem::ACTION_DOWNLOAD,    "label"=> t('Download'), 		"url"=>\URL::to('/ccm/system/file/download') , 'icon' => 'download'),

			"ccm_properties" => array( "type" => FileMenuItem::ACTION_OPEN_DIALOG, "label" => t('Edit Properties'), "url" => \URL::to('/ccm/system/dialogs/file/bulk/properties'), 'icon' => 'sliders',
									   		'options' =>  array( "width"=> "630", "height" => 450 )),

			"ccm_sets"		 => array( "type" => FileMenuItem::ACTION_OPEN_DIALOG, "label" => t('Sets'),            "url" => $uh->getToolsURL('files/add_to'), 'icon' => 'reorder',		// TODO refactor and remove tools once and for all
											"options" => array( "width"=>"500", "height"=>"400", )),

            "ccm_rescan"	 => array( "type" => FileMenuItem::ACTION_AJAX_REQUEST,   								"url"=>\URL::to('/ccm/system/file/rescan'), "label" => t('Rescan'), 'icon' => 'refresh'),

			# "ccm_duplicate"  => array( "type" => FileMenuItem::ACTION_OPEN_DIALOG, "label" => t('Copy'),            "url" => REL_DIR_FILES_TOOLS_REQUIRED . "files/duplicate",
			# 							"options" => array ( "title" => t('Duplicate'), "width"=> "500", "height" => 400, ) ),

			"ccm_dangerous"  => array( 'type' => FileMenuItem::ACTION_SEPARATOR ),

			"ccm_delete"	 => array( "type" => FileMenuItem::ACTION_OPEN_DIALOG, "label" => t('Delete'),          "url" => \URL::to('/ccm/system/dialogs/file/bulk/delete'), 'icon' => 'trash', 'dangerous' => true,
									   		"options" => array ( "title" => t('Delete'), "width"=> "500", "height" => 400, )),
        );


		foreach ( $default as $handle => $desc )
		{

			$item = new FileMenuItem( $handle );
			$item->setController( new ItemController() );

			$item->setActionType ( $desc['type'] );

			if ( isset( $desc['dangerous'] ) ) $item->setDangerous(true);

			if ( FileMenuItem::ACTION_SEPARATOR != $item->getActionType() )
			{
				$item->setLabel( $desc['label'] );
				$item->setLink ( $desc['url'] );

				if (isset($desc['icon'] ) ) $item->setIcon ( $desc['icon'] );

				if ( isset( $desc['options'] ) )
				{
					foreach( $desc['options'] as $key => $val ) $item->setLinkAttribute( "data-filemenu-$key", $val );
				}
			}
			$this->addMenuItem( $item );
		}
	}


	public function removeMenuItem( $handle ) 
	{ 
		if ( isset( $this->menu[$handle] ) ) unset( $this->menu[$handle] ); 

		if ( !is_null ($this->_bulkMenu ) ) $this->_bulkMenu = $this->_getBulkMenu();
	}

	public function addMenuItem( FileMenuItem $item ) { $this->menu[] = $item; }

	# TODO: insertMenuItemAfter removeMenuItem replaceMenuItem

	public function getMenuItemByHandle( $handle )
	{
		foreach ( $this->menu as $item ) {
			if ( $item->getHandle() == $handle ) {
				return $item;
			}
		}
	}

	public function getMenuItemByPosition( $idx ) { return $this->menu[$idx]; }

	protected function _getBulkMenu()
	{
		$menu = array();
		foreach ( $this->menu as $item ) {
			if ( $item->handleMultiple() ) $menu[] = $item;
		}
		return $menu;
	}

	public function getBulkMenu() 
	{ 
		if ( is_null( $this->_bulkMenu ) ) {
			\Events::dispatch('on_filemanager_bulk_menu');
			$this->_bulkMenu = $this->_getBulkMenu();
		}
		return $this->menu; 
	}
}
