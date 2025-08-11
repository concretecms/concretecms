<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Express\Association\Applier;
use Concrete\Core\Express\ObjectManager;

class UpdateExpressEntryCommandHandler
{

    use ExpressEntryCommandHandlerTrait;

    /**
     * @var Applier
     */
    protected $applier;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(Applier $applier, ObjectManager $objectManager)
    {
        $this->applier = $applier;
        $this->objectManager = $objectManager;
    }

    public function __invoke(UpdateExpressEntryCommand $command)
    {
        $entry = $command->getEntry();
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
