<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Express\Association\Applier;

class UpdateExpressEntryCommandHandler
{

    use ExpressEntryCommandHandlerTrait;

    /**
     * @var Applier
     */
    protected $applier;

    public function __construct(Applier $applier)
    {
        $this->applier = $applier;
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

        return $entry;
    }


}
