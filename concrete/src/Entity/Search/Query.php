<?php
namespace Concrete\Core\Entity\Search;

use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Utility\Service\Xml;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @ORM\Embeddable
 */
class Query implements \JsonSerializable, DenormalizableInterface
{
    const MAX_ITEMS_PER_PAGE = 10;

    /**
     * @ORM\Column(type="object")
     */
    protected $fields = [];

    /**
     * @ORM\Column(type="object")
     */
    protected $columns;

    /**
     * @ORM\Column(type="smallint")
     */
    private $itemsPerPage;

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Adds a field to the query object.
     *
     * @param FieldInterface $field
     */
    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param mixed $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fields' => $this->fields,
            'columnSet' => $this->columns,
            'itemsPerPage' => $this->getItemsPerPage(),
        ];
    }

    /**
     * @param int
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = is_numeric($itemsPerPage) ? $itemsPerPage : self::MAX_ITEMS_PER_PAGE;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $searchProvider = $context['searchProvider'];
        /**
         * @var $searchProvider ProviderInterface
         */
        $fieldManager = $searchProvider->getFieldManager();
        foreach($data['fields'] as $fieldRecord) {
            $field = $fieldManager->getFieldByKey($fieldRecord['key']);
            $field = $denormalizer->denormalize($fieldRecord, get_class($field), 'json', $context);
            $this->fields[] = $field;
        }
        $columnSet = $searchProvider->getBaseColumnSet();
        $all = $searchProvider->getAllColumnSet();
        foreach($data['columnSet']['columns'] as $columnRecord) {
            $column = $all->getColumnByKey($columnRecord['columnKey']);
            $columnSet->addColumn($column);
            if ($data['columnSet']['sortColumn'] == $columnRecord['columnKey']) {
                $columnSet->setDefaultSortColumn($column);
            }
        }
        $this->setColumns($columnSet);
        $this->itemsPerPage = $data['itemsPerPage'];
    }


}
