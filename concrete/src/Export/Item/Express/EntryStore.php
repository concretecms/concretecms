<?php
namespace Concrete\Core\Export\Item\Express;

use Concrete\Core\Entity\Express\Entry as ExpressEntry;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Utility\Service\Identifier;

class EntryStore
{

    protected $identifier;

    public function __construct(Identifier $identifier)
    {
        $this->identifier = $identifier;
    }

    protected $entryIDs = array();

    public function convertNumericEntryIdToIdentifier($id)
    {
        if (isset($this->entryIDs[$id])) {
            return $this->entryIDs[$id];
        } else {
            $identifier = $this->identifier->getString(12);
            $this->entryIDs[$id] = $identifier;
            return $identifier;
        }
    }

}
