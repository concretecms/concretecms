<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\Association\Builder\OneToOneAssociationBuilder;
use Concrete\Core\Express\Association\Formatter\OneToOneFormatter;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OneToOneAssociation extends Association
{
    const TYPE_OWNING = 'owning';
    const TYPE_INVERSE = 'inverse';

    /**
     * @ORM\Column(type="string")
     */
    protected $association_type;

    /**
     * @return mixed
     */
    public function getAssociationType()
    {
        return $this->association_type;
    }

    /**
     * @param mixed $association_type
     */
    public function setAssociationType($association_type)
    {
        $this->association_type = $association_type;
    }

    public function getAssociationBuilder()
    {
        return new OneToOneAssociationBuilder($this);
    }

    public function getFormatter()
    {
        return new OneToOneFormatter($this);
    }

    public function getSaveHandler()
    {
        return \Core::make('\Concrete\Core\Express\Form\Control\SaveHandler\OneToOneAssociationSaveHandler');
    }


}
