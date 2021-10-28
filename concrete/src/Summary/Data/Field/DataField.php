<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\Summary\Field;
use Doctrine\Common\Collections\ArrayCollection;

final class DataField implements DataFieldInterface
{

    /**
     * @var DataFieldDataInterface
     */
    protected $data;

    /**
     * @var string
     */
    protected $fieldIdentifier;

    /**
     * DataField constructor.
     * @param string $fieldIdentifier   
     * @param string|int|bool|DataFieldDataInterface $data
     */
    public function __construct(string $fieldIdentifier, $data)
    {
        $this->fieldIdentifier = $fieldIdentifier;
        if (!($data instanceof DataFieldDataInterface)) {
            $data = new DataFieldData($data);
        }
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData() : DataFieldDataInterface
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
