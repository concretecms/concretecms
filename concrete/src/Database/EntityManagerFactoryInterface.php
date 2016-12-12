<?php
namespace Concrete\Core\Database;

use Concrete\Core\Database\Connection\Connection;

interface EntityManagerFactoryInterface
{
    public function create(Connection $connection);
}
