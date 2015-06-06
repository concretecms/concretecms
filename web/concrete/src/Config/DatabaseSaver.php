<?php

namespace Concrete\Core\Config;

class DatabaseSaver implements SaverInterface
{
    /**
     * Save config item.
     *
     * @param string      $item
     * @param string      $value
     * @param string      $environment
     * @param string      $group
     * @param string|null $namespace
     *
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

        $query = \Database::createQueryBuilder();
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
                        $namespace ?: '',
                    ));
            } catch (\Exception $e) {
                // This happens when the update succeeded, but didn't actually change anything on the row.
            }
        }
    }

    /**
     * Reset a config item.
     *
     * @param string $item
     * @param string $environment
     * @param string $group
     * @param string|null $namespace
     *
     * @return bool
     */
    public function reset($item, $environment, $group, $namespace = null)
    {
        $query = \Database::createQueryBuilder();
        $query->delete('Config');
        if ($namespace) {
            $query->andWhere($query->expr()->eq('configNamespace', $query->expr()->literal($namespace)));
        } else {
            $query->andWhere($query->expr()->orX(
                $query->expr()->isNull('configNamespace'),
                $query->expr()->eq('configNamespace', $query->expr()->literal(''))
            ));
        }
        $query->andWhere($query->expr()->eq('configGroup', $query->expr()->literal($group)));
        if ($item) {
            $query->andWhere($query->expr()->eq('configItem', $query->expr()->literal($item)));
        } else {
            $query->andWhere($query->expr()->orX(
                $query->expr()->isNull('configItem'),
                $query->expr()->eq('configItem', $query->expr()->literal(''))
            ));
        }
        $query->execute();
        return true;
    }
}
