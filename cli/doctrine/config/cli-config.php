<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

define('DIR_BASE', realpath(dirname(__FILE__) . '/../../../web'));
$DIR_BASE_CORE = realpath(dirname(__FILE__) . '/../../../web/concrete');

require $DIR_BASE_CORE . '/bootstrap/configure.php';
require $DIR_BASE_CORE . '/bootstrap/autoload.php';
$cms = require $DIR_BASE_CORE . '/bootstrap/start.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $db = Loader::db()->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
