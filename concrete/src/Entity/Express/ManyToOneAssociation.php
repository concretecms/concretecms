<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\Association\Formatter\ManyToOneFormatter;
use Concrete\Core\Express\Association\Builder\ManyToOneAssociationBuilder;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ManyToOneAssociation extends Association
{
    public function getAssociationBuilder()
    {
        return new ManyToOneAssociationBuilder($this);
    }

    public function getFormatter()
    {
        return new ManyToOneFormatter($this);
    }

    public function getSaveHandler()
    {
        return \Core::make('\Concrete\Core\Express\Form\Control\SaveHandler\ManyToOneAssociationSaveHandler');
    }


}
