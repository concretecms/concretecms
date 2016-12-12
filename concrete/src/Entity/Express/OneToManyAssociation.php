<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\Association\Builder\OneToManyAssociationBuilder;
use Concrete\Core\Express\Association\Formatter\OneToManyFormatter;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OneToManyAssociation extends Association
{
    public function getAssociationBuilder()
    {
        return new OneToManyAssociationBuilder($this);
    }

    public function getFormatter()
    {
        return new OneToManyFormatter($this);
    }

    public function getSaveHandler()
    {
        return \Core::make('\Concrete\Core\Express\Form\Control\SaveHandler\OneToManyAssociationSaveHandler');
    }


}
