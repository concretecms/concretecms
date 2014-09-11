<?php
namespace Concrete\Core\Config;

use Concrete\Core\Database\Database;

class DatabaseSaver implements SaverInterface {

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
        $db = Database::getActiveConnection();

        $query = $db->createQueryBuilder();




        $query->update('Config', 'c')
            ->set('configValue', $query->expr()->literal($value))
            ->where($query->expr()->comparison('configItem', '=', $query->expr()->literal($item)))
            ->andWhere($query->expr()->comparison('configGroup', '=', $query->expr()->literal($group)));

        if ($namespace) {
            $query->andWhere('namespace = ?')
                ->setParameter(2, $namespace);
        }

        var_dump($query->execute());
        if (!$success) {
            try {
                $query = "INSERT INTO Config (configItem, configValue, configGroup, configNamespace) VALUES (?, ?, ?, ?)";
                $db->executeQuery(
                    $query,
                    array(
                        $item,
                        $value,
                        $group,
                        $namespace
                    ));
            } catch (\Exception $e) {
                // This happens when the update succeeded, but didn't actually change anything on the row.
            }
        }
    }

}
