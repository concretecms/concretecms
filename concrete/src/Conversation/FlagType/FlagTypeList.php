<?php
namespace Concrete\Core\Conversation\FlagType;

use ConversationFlagType;
use Concrete\Core\Legacy\DatabaseItemList;

class FlagTypeList extends DatabaseItemList
{
    public function __construct()
    {
        $this->setQuery('select * from ConversationFlaggedMessageTypes');
        $this->sortBy('cnvMessageFlagTypeID', 'asc');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $r = parent::get($itemsToGet, $offset);
        $flagTypes = array();
        foreach ($r as $row) {
            $flagTypes[] = ConversationFlagType::getByID($row['cnvMessageFlagTypeID']);
        }

        return $flagTypes;
    }
}
