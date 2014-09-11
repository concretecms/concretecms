<?php
namespace Concrete\Core\Config;

use Concrete\Core\Database\Database;

class DatabaseSaver implements SaverInterface
{

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
        if (is_array($value)) {

            foreach ($value as $key => $val) {
                $key = ($item ? $item . '.' : '') . $key;

                $this->save($key, $val, $environment, $group, $namespace);
            }
            return;
        }
        $db = Database::getActiveConnection();

        $query = $db->createQueryBuilder();

        $query->update('Config', 'c')
              ->set('configValue', $query->expr()->literal($value))
              ->where($query->expr()->comparison('configGroup', '=', $query->expr()->literal($group)));

        if ($item) {
            $query->andWhere($query->expr()->comparison('configItem', '=', $query->expr()->literal($item)));
        }

        if ($namespace) {
            $query->andWhere($query->expr()->comparison('configNamespace', '=', $query->expr()->literal($namespace)));
        }

        if (!$query->execute()) {
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
