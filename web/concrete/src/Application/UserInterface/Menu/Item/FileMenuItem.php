<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

class FileMenuItem extends Item
{
    const ACTION_AJAX_REQUEST = 'ajax';     //!< @javascript-exported
    const ACTION_OPEN_DIALOG  = 'dialog';   //!< @javascript-exported
    const ACTION_DOWNLOAD     = 'download'; //!< @javascript-exported
    const ACTION_OPEN         = 'open';     //!< @javascript-exported
    const ACTION_SEPARATOR    = 'separator';//!< @javascript-exported


    protected $actionType = self::ACTION_OPEN_DIALOG;

    public function getActionType()
    { 
        return $this->actionType; 
    }

    public function setActionType($type) 
    { 
        $this->setLinkAttribute('data-filemenu-type', $type);
        $this->actionType = $type; 
    }

    const ABILITY_UNIQUE        = 1;
    const ABILITY_MULTIPLE      = 2;
    const ABILITY_ALL           = 3;

    protected $abilities    = self::ABILITY_ALL;

    public function handleMultiple()
    { 
        return $this->abilities & self::ABILITY_MULTIPLE; 
    }

    public function handleUnique()
    { 
        return $this->abilities & self::ABILITY_UNIQUE; 
    }

    public function setAbilities($abilities)
    { 
        $this->abilities = $abilities;
    }
    
    public function hasAbility($ability)
    {
        return ($this->abilities & $ability) == $ability;
    }

    public function isSeparator()
    {
        return self::ACTION_SEPARATOR == $this->actionType;
    }


    protected $dangerous      = false;

    public function isDangerous()
    {
        return $this->dangerous;
    }

    public function setDangerous($dangerous = true)
    {
        $this->dangerous = $dangerous;
    }

    public function getLinkAttributes()
    {
        $attrs = parent::getLinkAttributes();
        $attrs['data-filemenu-type'] = $this->getActionType();
        return $attrs;
    }

    protected $restrictions = array();

    const CAN_VIEW     = 'canViewFile';
    const CAN_EDIT     = 'canEditFile';
    const CAN_REPLACE  = 'canReplaceFile';
    const CAN_COPY     = 'canCopyFile';
    const CAN_DELETE   = 'canDeleteFile';
    const CAN_PERMS    = 'canEditFilePermissions';

    public function getRestrictions()
    {
        return $this->restrictions;
    }

    public function addRestriction($restrict)
    { 
        $this->restrictions[] = $restrict;
    }

    public function setRestrictions(array $restrict)
    {
        $this->restrictions = $restrict;
    }
}
