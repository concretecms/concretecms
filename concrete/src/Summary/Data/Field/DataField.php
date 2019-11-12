<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\Summary\Field;
use Doctrine\Common\Collections\ArrayCollection;

class DataField implements DataFieldInterface
{

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $fieldIdentifier;

    /**
     * DataField constructor.
     * @param string $fieldIdentifier   
     * @param mixed $data
     */
    public function __construct(string $fieldIdentifier, $data)
    {
        $this->fieldIdentifier = $fieldIdentifier;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getFieldIdentifier(): string
    {
        return $this->fieldIdentifier;
    }

    


}
