<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Express\Association\Applier;
use Concrete\Core\Express\Entry\Manager;
use Concrete\Core\Express\ObjectManager;

class AddExpressEntryCommandHandler
{

    use ExpressEntryCommandHandlerTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Manager
     */
    protected $entryManager;

    /**
     * @var Applier
     */
    protected $applier;

    public function __construct(ObjectManager $objectManager, Manager $entryManager, Applier $applier)
    {
        $this->objectManager = $objectManager;
        $this->entryManager = $entryManager;
        $this->applier = $applier;
    }

    public function __invoke(AddExpressEntryCommand $command)
    {
        $object = $command->getEntity();
        $entry = $this->entryManager->addEntry($object);
        $map = $command->getAttributeMap();
        if ($map) {
            $this->handleAttributeMap($map, $entry);
        }

        $map = $command->getAssociationMap();
        if ($map) {
            $this->handleAssociationMap($map, $entry);
        }

        $this->objectManager->refresh($entry);
        return $entry;
    }


}
