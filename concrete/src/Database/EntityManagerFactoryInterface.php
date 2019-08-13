<?php
namespace Concrete\Core\Database;

use Concrete\Core\Database\Connection\Connection;

/**
 * @since 5.7.5.3
 */
interface EntityManagerFactoryInterface
{
    public function create(Connection $connection);
}
