<?php

declare(strict_types=1);

use Concrete\Core\Support\CodingStyle\PHPCSFixerConfigurator;

define('DIR_BASE', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));

require_once DIR_BASE . '/concrete/bootstrap/configure.php';

require_once DIR_BASE_CORE . '/src/Support/CodingStyle/autoload.php';

$minimumPHPVersion = $_ENV['C5_PHPCS_MIN_PHP_VERSION'] ?? '';
if (!is_string($minimumPHPVersion) || ($minimumPHPVersion = trim($minimumPHPVersion)) === '') {
    $minimumPHPVersion = getenv('C5_PHPCS_MIN_PHP_VERSION');
    if (!is_string($minimumPHPVersion) || ($minimumPHPVersion = trim($minimumPHPVersion)) === '') {
        $minimumPHPVersion = '7.3';
    }
}
$minimumPHPVersion = PHPCSFixerConfigurator::parseMinimumPHPVersionFormat($minimumPHPVersion);
echo "Fixing using rules for PHP {$minimumPHPVersion}\nYou can change it setting the C5_PHPCS_MIN_PHP_VERSION environment variable\n";

return (new PHPCSFixerConfigurator($minimumPHPVersion))
    ->setEnvironmentVariables()
    ->createConfig()
;
