<?php
namespace Concrete\Core\Config;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Database;
use Doctrine\DBAL\Driver\PDOStatement;

class DatabaseSaver implements SaverInterface
{

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @return Connection
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = Database::connection();
        }
        return $this->connection;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Save config item
     *
     * @param string      $item
     * @param string      $value
     * @param string      $environment
     * @param string      $group
     * @param string|null $namespace
     * @return bool
     */
    public function save($item, $value, $environment, $group, $namespace = null)
    {
        $builder = $this->getConnection()->createQueryBuilder();

        $query = $builder->delete('Config')
            ->where('configNamespace = :namespace',
                    'configGroup = :group',
                    'configItem LIKE :item')
            ->setParameters(array(
                ':namespace' => $namespace ?: '',
                ':group' => $group,
                ':item' => "{$item}.%"
            ));
        $amount_deleted = $query->execute();

        $this->doSave($item, $value, $environment, $group, $namespace);
    }

    private function doSave($item, $value, $environment, $group, $namespace = null)
    {
        $connection = $this->getConnection();
        $query = $connection->createQueryBuilder();

        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $key = ($item ? $item . '.' : '') . $key;
                $this->doSave($key, $val, $environment, $group, $namespace);
            }

            return;
        }
        $query->update('Config', 'c')
              ->set('configValue', $query->expr()->literal($value))
              ->where($query->expr()->comparison('configGroup', '=', $query->expr()->literal($group)));

        if ($item) {
            $query->andWhere($query->expr()->comparison('configItem', '=', $query->expr()->literal($item)));
        }

        $query->andWhere($query->expr()->comparison('configNamespace', '=', $query->expr()->literal($namespace ?: '')));

        if (!$query->execute()) {
            try {
                $query = "INSERT INTO Config (configItem, configValue, configGroup, configNamespace) VALUES (?, ?, ?, ?)";
                \Database::executeQuery(
                    $query,
                    array(
                        $item,
                        $value,
                        $group,
                        $namespace ?: ''
                    ));
            } catch (\Exception $e) {
                // This happens when the update succeeded, but didn't actually change anything on the row.
            }
        }
    }

}
