<?php

namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Config;
use Exception;

class ConfigCommand extends Command
{
    const OPERATION_GET = 'get';
    const OPERATION_SET = 'set';

    protected function configure()
    {
        $this
            ->setName('c5:config')
            ->setDescription('Set or get configuration parameters.')
            ->addArgument('operation', InputArgument::REQUIRED, 'The operation to accomplish ('.implode('|', self::getAllowedOperations()).')')
            ->addArgument('key', InputArgument::REQUIRED, 'The configuration key (eg: concrete.debug.display_errors)')
            ->addArgument('value', InputArgument::OPTIONAL, 'The new value of the configuration key')
            ->addOption('environment', 'e', InputOption::VALUE_REQUIRED, 'The environment (if not specified, we\'ll work with the configuration keys valid for all environments)')
        ;
        $this->setHelp(<<<EOT
When setting values that may be evaluated as boolean (true/false), null or numbers, but you want to store them as strings, you can enclose those values in single or double quotes.
For instance, with
concrete5 %command.name% set concrete.test_key 1
The new configuration key will have a numeric value of 1. If you want to save the string "1" you have to write
concrete5 %command.name% set concrete.test_key '1'
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $key = $input->getArgument('key');
            if (!self::isValidConfigurationKeyName($key)) {
                throw new Exception("Invalid configuration key: '$key'");
            }
            switch ($input->getArgument('operation')) {
                case self::OPERATION_GET:
                    $output->writeln(self::serialize(\Config::get($key)));
                    break;
                case self::OPERATION_SET:
                    $value = $input->getArgument('value');
                    if (!isset($value)) {
                        throw new Exception('Missing new configuration value');
                    }
                    \Config::save($key, self::unserialize($value));
                    break;
                default:
                    throw new Exception('Invalid operation specified. Allowed operations: '.implode(', ', self::getAllowedOperations()));
            }
        } catch (Exception $x) {
            $output->writeln('<error>'.$x->getMessage().'</error>');
            $rc = 1;
        }

        return $rc;
    }

    /**
     * @return string[]
     */
    protected static function getAllowedOperations()
    {
        return array(
            self::OPERATION_GET,
            self::OPERATION_SET,
        );
    }

    /**
     * @param string $environment
     *
     * @return bool
     */
    protected static function isValidConfigurationKeyName($key)
    {
        return (is_string($key) && preg_match('/^[a-z]\w*(\.\w+)+$/i', $key)) ? true : false;
    }

    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws Exception
     */
    protected static function serialize($value)
    {
        $jsonOptions = 0;
        if (defined('JSON_UNESCAPED_SLASHES')) {
            $jsonOptions |= JSON_UNESCAPED_SLASHES;
        }
        $type = gettype($value);
        $result = null;
        switch ($type) {
            case 'boolean':
                $result = $value ? 'true' : 'false';
                break;
            case 'NULL':
                $result = 'null';
                break;
            case 'integer':
            case 'double':
                $result = (string) $value;
                break;
            case 'string':
                $enquote = false;
                switch ($value) {
                    case 'true':
                    case 'false':
                    case 'null':
                        $enquote = true;
                    default:
                        if (preg_match('/^-?\d+(\.\d*)?$/', $value)) {
                            $enquote = true;
                        }
                        break;
                }
                $result = $enquote ? "\"$value\"" : $value;
                break;
        }
        if (!isset($result)) {
            throw new Exception("Unable to represent variable of type '$type'");
        }

        return $result;
    }

    /**
     * @param string $value
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected static function unserialize($value)
    {
        if (!(is_string($value))) {
            throw new Exception('Invalid value');
        }
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }
        if (preg_match('/^-?\d+$/', $value)) {
            return (int) $value;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        foreach (array('"', "'") as $q) {
            if (preg_match('/^'.$q.'(true|false|null|-?\d+(\.\d*)?)'.$q.'$/', $value)) {
                return substr($value, 1, -1);
            }
        }

        return $value;
    }
}
