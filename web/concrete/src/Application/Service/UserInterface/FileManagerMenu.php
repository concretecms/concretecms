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

            "ccm_view"       => array( "label"=> t('View'), "url"=>\URL::to('/ccm/system/file/view'),
                                       "type" => FileMenuItem::ACTION_OPEN_DIALOG, 'perms' => array( FileMenuItem::CAN_VIEW ), 'ability' => FileMenuItem::ABILITY_UNIQUE,
                                       "options" => array( 'modal' => 'false', 'height' =>  '75%', 'width' => '90%') ),

            "ccm_download"   => array( "label"=> t('Download'), "url"=>\URL::to('/ccm/system/file/download'),
                                       "type" => FileMenuItem::ACTION_DOWNLOAD, 'perms' => array(FileMenuItem::CAN_VIEW) ),

            "ccm_edit"       => array( "label"=> t('Edit'), "url"=> $uh->getToolsURL('files/edit'), // TODO refactor tool to a route
                                       "type" => FileMenuItem::ACTION_OPEN_DIALOG, 'perms' => array( FileMenuItem::CAN_EDIT), 'ability' => FileMenuItem::ABILITY_UNIQUE,
                                       "options" => array( 'modal' => 'true', 'height' =>  '70%', 'width' => '90%') ),

            "ccm_bulk_properties" => array( "label" => t('Properties'), "url" => \URL::to('/ccm/system/dialogs/file/bulk/properties'), 
                                        "type" => FileMenuItem::ACTION_OPEN_DIALOG,  'ability' => FileMenuItem::ABILITY_MULTIPLE,
                                        'options' =>  array( "width"=> "680", "height" => 450 )),

            "ccm_properties" => array( "label" => t('Properties'), "url" => \URL::to('/ccm/system/dialogs/file/properties'), 
                                        "type" => FileMenuItem::ACTION_OPEN_DIALOG,  'ability' => FileMenuItem::ABILITY_UNIQUE,
                                        'options' =>  array( "width"=> "680", "height" => 450 )),

            "ccm_replace"    => array( "label"=> t('Replace'), "url"=> $uh->getToolsURL('files/replace'), // TODO refactor tool to a route
                                       "type" => FileMenuItem::ACTION_OPEN_DIALOG, 'perms' => array( FileMenuItem::CAN_REPLACE), 'ability' => FileMenuItem::ABILITY_UNIQUE,
                                       "options" => array( 'modal' => 'true', 'height' =>  '500', 'width' => '200')),

            "ccm_duplicate"  => array( "label" => t('Duplicate'), "url" => \URL::to('/ccm/system/file/duplicate'),// FIXME: For now duplicate is broken on Doctrine side
                                       "type" => FileMenuItem::ACTION_AJAX_REQUEST, 'perms' => array( FileMenuItem::CAN_COPY)),

            "ccm_sets"       => array( "label" => t('Sets'), "url" => $uh->getToolsURL('files/add_to'),      // TODO refactor and remove tools once and for all
                                        "type" => FileMenuItem::ACTION_OPEN_DIALOG, 
                                        "options" => array( "width"=>"500", "height"=>"400", )),

            "ccm_rescan"     => array( "label" => t('Rescan'), "url"=>\URL::to('/ccm/system/file/rescan'),
                                        "type" => FileMenuItem::ACTION_AJAX_REQUEST ),

            "ccm_dangerous"  => array( 'type' => FileMenuItem::ACTION_SEPARATOR ),

            "ccm_perms"      => array( "label" => t('Permissions'), "url" => $uh->getToolsURL('files/permissions'),      // TODO refactor and remove tools once and for all
                                        "type" => FileMenuItem::ACTION_OPEN_DIALOG, 'perms' => array( FileMenuItem::CAN_PERMS ), 'ability' => FileMenuItem::ABILITY_UNIQUE, // TODO: This tool would be nice as ABILITY_ALL
                                        "options" => array( "width"=>"500", "height"=>"400", )),

            "ccm_delete"     => array( "label" => t('Delete'),  "url" => \URL::to('/ccm/system/dialogs/file/bulk/delete'),
                                        "type" => FileMenuItem::ACTION_OPEN_DIALOG, 'dangerous' => true, 'perms' => array( FileMenuItem::CAN_DELETE ),
                                        "options" => array ( "width"=> "500", "height" => 400, )),
        );


        foreach ($default as $handle => $desc) {

            $item = new FileMenuItem($handle);

            $item->setActionType($desc['type']);

            if (isset($desc['dangerous'])) $item->setDangerous(true);
            if (isset($desc['perms']))     $item->setRestrictions($desc['perms']);
            if (isset($desc['ability']))   $item->setAbilities($desc['ability']);

            if (FileMenuItem::ACTION_SEPARATOR != $item->getActionType()) {
                $item->setLabel($desc['label']);
                $item->setLink($desc['url']);

                if (isset($desc['icon'])) $item->setIcon($desc['icon']);

                if (isset($desc['options'])) {
                    foreach ($desc['options'] as $key => $val) $item->setLinkAttribute("data-filemenu-$key", $val);
                }
            }
            $this->addMenuItem($item);
        }
    }


    public function addMenuItem(FileMenuItem $item, ItemControllerInterface $controller = NULL) 
    {
        if (!$controller) $controller = new ItemController();
        $item->setController( $controller );
        $this->menu[] = $item; 
    }

    public function getItemPosition ( $handleOrObject ) 
    {
        $handle = $handleOrObject;
        if (is_object($handleOrObject)) $handle = $handleOrObject->getHandle();
        foreach ($this->menu as $key => $item) {
            if ($item->getHandle() == $handle) return $key;
        }
        return false;
    }

    public function removeMenuItem( $handle )
    {
        $idx = $this->getItemPosition($handle);
        if (false !== $idx) unset ($this->menu[$idx]);
    }

    public function replaceMenuItem( FileMenuItem $newItem )
    {
        $idx = $this->getItemPosition($newItem->getHandle());
        if (false !== $idx) $this->menu[$idx] = $newItem;
    }

    public function insertMenuItemAfter( $handleOrItem, $newItem )
    {
        $idx = $this->getItemPosition($handleOrItem);
        if ( false === $idx ) return;
        array_splice($this->menu, $idx, 0, array($newItem));
    }

    public function getMenuItemByHandle($handle)
    {
        foreach ($this->menu as $item) {
            if ($item->getHandle() == $handle) return $item;
        }
        return null;
    }

    public function getMenuItemByPosition( $idx )
    {
        return $this->menu[$idx];
    }

    protected function getFilteredMenu( $ability )
    {
        $menu = array();
        foreach ($this->menu as $item) {
            if ($item->hasAbility($ability)) $menu[] = $item;
        }
        return $menu;
    }

    public function getBulkMenu()
    { 
        static $eventFired = false;

        if (!$eventFired) {
            $eventFired = true;
            \Events::dispatch('on_file_manager_bulk_menu');
        }

        return $this->getFilteredMenu(FileMenuItem::ABILITY_MULTIPLE);
    }

    public function getFileContextMenu()
    {
        static $eventFired = false;
        if (!$eventFired) {
            $eventFired = true;
            \Events::dispatch('on_file_manager_context_menu');
        }

        return $this->getFilteredMenu(FileMenuItem::ABILITY_UNIQUE);
    }
}
