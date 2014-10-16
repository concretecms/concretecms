<?php
namespace Concrete\Core\Config;

use Concrete\Core\Database\Driver\PDOStatement;
use Database;

class DatabaseLoader implements LoaderInterface
{

    /**
     * Load the given configuration group. Because it's the database, we ignore the environment.
     *
     * @param  string $environment
     * @param  string $group
     * @param  string $namespace
     * @return array
     */
    public function load($environment, $group, $namespace = null)
    {
        $result = array();

        $db = Database::getActiveConnection();
        $query = $db->createQueryBuilder();

        $query
            ->select('configValue', 'configItem')
            ->from('Config', 'c')
            ->where('configGroup = ?')
            ->setParameter(0, $group);

        if ($namespace) {
            $query->andWhere('configNamespace = ?')->setParameter(1, $namespace);
        } else {
            $query->andWhere('configNamespace = ? OR configNamespace IS NULL')->setParameter(1, '');
        }

        $results = $query->execute();

        while ($row = $results->fetch()) {
            array_set($result, $row['configItem'], $row['configValue']);
        }

        return $result;
    }

    /**
     * Determine if the given configuration group exists.
     *
     * @param  string $group
     * @param  string $namespace
     * @return bool
     */
    public function exists($group, $namespace = null)
    {
        $db = Database::getActiveConnection();
        $query = $db->createQueryBuilder();

        $query
            ->select('count(configGroup)')
            ->from('Config', 'c')
            ->where('configGroup = ?')
            ->setParameter(0, $group)
            ->setMaxResults(1);

        if ($namespace) {
            $query->andWhere('configNamespace = ?')->setParameter(1, $namespace);
        } else {
            $query->andWhere('configNamespace = ? OR configNamespace IS NULL')->setParameter(1, '');
        }

        $count = array_shift($query->execute()->fetch());
        return !!$count;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string $namespace
     * @param  string $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        // This is unused.
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        $db = Database::getActiveConnection();

        /** @var PDOStatement $results */
        $results = $db->createQueryBuilder()
            ->select('configNamespace')
            ->from('Config', 'c')
            ->where('configNamespace != ""')
            ->groupBy('configNamespace')
            ->execute();

        return array_map('array_shift', $results->fetchAll());
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param  string $environment
     * @param  string $package
     * @param  string $group
     * @param  array  $items
     * @return array
     */
    public function cascadePackage($environment, $package, $group, $items)
    {
        return $items;
    }

    public function clearNamespace($namespace)
    {
        if ($namespace) {
            $db = Database::getActiveConnection();

            $query = $db->createQueryBuilder();
            $query
                ->delete('Config', 'c')
                ->where($query->expr()->comparison('configNamespace', '=', $query->expr()->literal($namespace)));

        }
    }

}
