<?php

namespace Concrete\Core\Block;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Doctrine\DBAL\Types\Type;

abstract class ItemListBlockController extends BlockController
{

    /**
     * @var Connection
     */
    protected $db;

    /**
     * Item List table: list of fields and other list prepared of insert query
     * @var array
     */
    private $itemListTableFields = [];

    /**
     * Return Items table name
     * @return string
     */
    protected abstract function getItemListTable();

    /**
     * Items table: load and get list of fields and other list prepared of insert query
     * @param string $prop (optional) can be FIELD_TYPE, DEFAULT, NOTNULL, QUERY_PLACE_HOLDER,
     * @return array
     */
    private function getItemListTableProps($prop = '')
    {
        $cache = $this->app->make('cache/request');

        if(empty($this->itemListTableFields)) {
            $item = $cache->getItem(sprintf('block/%s/2nd-table-cols', $this->btHandle));
            if (!$item->isMiss()) {
                $this->itemListTableFields = $item->get();
            } else {
                $columns = $this->db->getSchemaManager()->listTableColumns($this->getItemListTable());
                foreach ($columns as $column) {
                    if(!$column->getAutoincrement()) {
                        $this->itemListTableFields[$column->getName()] = [
                            'FIELD_TYPE' => $column->getType()->getName(),
                            'DEFAULT' => $column->getDefault(),
                            'NOTNULL' => $column->getNotnull(),
                            'QUERY_PLACE_HOLDER' => ":{$column->getName()}"
                        ];
                    }
                }
                $cache->save($item->set($this->itemListTableFields));
            }
        }

        if(!empty($prop)) {
            return array_combine(
                array_keys($this->itemListTableFields),
                array_column($this->itemListTableFields, $prop)
            );
        }

        return $this->itemListTableFields;
    }

    /**
     * {@inheritdoc}
     *
     * @see BlockController::duplicate()
     */
    public function duplicate($newBID)
    {
        parent::duplicate($newBID);

        $insertFields = $this->getItemListTableProps('QUERY_PLACE_HOLDER');
        $this->db->transactional(function (Connection $db) use ($insertFields, $newBID) {
            $qb = $db->createQueryBuilder()
                ->insert($this->getItemListTable())
                ->values($insertFields);

            foreach ($this->getItems() as $item) {
                $item['bID'] = $newBID;
                id(clone $qb)->setParameters($item)->execute();
            }
        });
    }

    /**
     * {@inheritdoc}
     *
     * @see BlockController::delete()
     */
    public function delete()
    {
        $this->deleteItems();
        parent::delete();
    }

    /**
     * Delete second table items
     */
    private function deleteItems()
    {
        $qb = $this->db->createQueryBuilder();
        $qb->delete($this->getItemListTable())
            ->where($qb->expr()->eq('bID', ':bID'))
            ->setParameter('bID', $this->bID)->execute();
    }

    /**
     * {@inheritdoc}
     * Automatically save Item List table data
     *
     * @see BlockController::save()
     */
    public function save($args)
    {
        parent::save($args);

        $sanitizedData = $this->sanitizeData($args);
        if(empty($sanitizedData)) {
            return;
        }

        $this->deleteItems();
        $insertFields = $this->getItemListTableProps('QUERY_PLACE_HOLDER');

        $this->db->transactional(function (Connection $db) use ($sanitizedData, $insertFields){
            $qb = $db->createQueryBuilder()
                ->insert($this->getItemListTable())
                ->values($insertFields);

            foreach ($sanitizedData as $item) {
                if($this->isValidItem($item)) {
                    id(clone $qb)->setParameters($item)->execute();
                }
            }
        });
    }

    /**
     * Prepare Second Table Fields Data before saving them to database
     * @param $data
     *
     * @return array sanitized array
     */
    private function sanitizeData($data)
    {
        $sanitizedData = [];
        $itemDefaults = $this->getItemDefaults();
        $fieldsTypes = $this->getItemListTableProps('FIELD_TYPE');

        foreach ($fieldsTypes as $field => $type) {

            if($field == 'bID' || !isset($data[$field])) {
                continue;
            }

            foreach ($data[$field] as $i => $v) {

                if (!isset($sanitizedData[$i])) {
                    // Init Row with defaults data
                    $sanitizedData[$i] = $itemDefaults;
                }

                $sanitizedData[$i][$field] = $this->sanitizeVal($field, $type, $v);
            }
        }

        return $sanitizedData;
    }

    /**
     * Get Row default Values from Table Schema
     * @return array
     */
    private function getItemDefaults()
    {
        $itemDefaults = ['bID' => $this->bID];
        foreach ($this->getItemListTableProps() as $field => $properties) {
            if($field == 'bID') {
                continue;
            }

            $itemDefaults[$field] = $this->sanitizeVal($field, $properties['FIELD_TYPE'], $properties['DEFAULT']);
        }

        return $itemDefaults;
    }

    /**
     * Prepare Items Table Field Value before saving it to database
     *
     * @param $field
     * @param $type
     * @param $value
     * @return mixed sanitized value
     */
    protected function sanitizeVal($field, $type, $value)
    {
        switch ($type) {
            case Type::INTEGER:
            case Type::SMALLINT:
                $sanitizedVal = (int) $value;
                break;
            case Type::STRING:
                $sanitizedVal = trim((string) $value);
                break;
            case Type::TEXT:
                $sanitizedVal = (string) $value;
                // Check if is Html
                if($sanitizedVal != strip_tags($sanitizedVal)) {
                    $sanitizedVal = LinkAbstractor::translateTo($sanitizedVal);
                }
                break;
            case Type::BOOLEAN:
                $sanitizedVal = $value ? 1 : 0;
                break;
            default:
                $sanitizedVal = $value;
        }

        return $sanitizedVal;
    }

    /**
     * Check whether the item is valid.
     * if so, the item will be saved otherwise it will be ignored
     *
     * @param array $item
     * @return bool
     */
    protected abstract function isValidItem(array $item);

    /**
     * Get list of Item List table items
     * @param array $sort
     * @return array
     */
    public function getItems($sort = [])
    {
        $qb = $this->db->createQueryBuilder()->select('*')->from($this->getItemListTable());
        $qb->where($qb->expr()->eq('bID', ':bID'))->setParameter('bID', $this->bID);

        foreach ($sort as $col => $dir) {
            $qb->addOrderBy($col, $dir);
        }

        return $qb->execute()->fetchAll();
    }

    /**
     * {@inheritdoc}
     *
     * @see BlockController::setApplication()
     */
    public function setApplication(Application $app)
    {
        parent::setApplication($app);
        $this->db = $app['database']->connection();
    }
}