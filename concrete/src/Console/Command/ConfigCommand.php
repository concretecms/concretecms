<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Config\DirectFileSaver;
use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\FileSaver;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Console\Command;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCommand extends Command
{
    const OPERATION_GET = 'get';
    const OPERATION_SET = 'set';

    protected $description = 'Set or get configuration parameters.';

    protected $signature = 'c5:config 
        {action : Either "get" or "set"} 
        {item : The config item EG: "concrete.debug.detail"} 
        {value? : The value to set}
        {--e|environment : The environment, if none specified the global configuration will be used}
        {--g|generated-overrides : Save to generated overrides}';

    /** @var Repository The main config repository */
    protected $config;

    /** @var Repository */
    protected $repository;

    public function __construct(Repository $config)
    {
        $this->config = $config;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setHelp(<<<EOT
When setting values that may be evaluated as boolean (true/false), null or numbers, but you want to store them as strings, you can enclose those values in single or double quotes.
For instance, with
concrete5 %command.name% set concrete.test_item 1
The new configuration item will have a numeric value of 1. If you want to save the string "1" you have to write
concrete5 %command.name% set concrete.test_item '1'

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-config
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $default_environment = $this->config->getEnvironment();

        $environment = $input->getOption('environment') ?: $default_environment;

        $file_system = new Filesystem();
        $file_loader = new FileLoader($file_system);
        if ($input->getOption('generated-overrides')) {
            $file_saver = new FileSaver($file_system, $environment == $default_environment ? null : $environment);
        } else {
            $file_saver = new DirectFileSaver($file_system, $environment == $default_environment ? null : $environment);
        }
        $this->repository = new Repository($file_loader, $file_saver, $environment);

        $item = $input->getArgument('item');
        switch ($input->getArgument('action')) {
            case self::OPERATION_GET:
                $output->writeln($this->serialize($this->repository->get($item)));
                break;

            case self::OPERATION_SET:
                $value = $input->getArgument('value');
                if (!isset($value)) {
                    throw new Exception('Missing new configuration value');
                }

                $this->repository->save($item, $this->unserialize($value));
                break;

            default:
                throw new Exception('Invalid action specified. Allowed actions: ' . implode(', ',
                        $this->getAllowedOperations()));
        }
    }

    /**
     * @return string[]
     */
    protected function getAllowedOperations()
    {
        return [
            self::OPERATION_GET,
            self::OPERATION_SET,
        ];
    }

    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws Exception
     */
    protected function serialize($value)
    {
        $jsonOptions = JSON_PRETTY_PRINT;
        if (defined('JSON_UNESCAPED_SLASHES')) {
            $jsonOptions |= JSON_UNESCAPED_SLASHES;
        }
        $type = gettype($value);
        $result = null;
        switch ($type) {
            case 'array':
                $result = json_encode($value, $jsonOptions);
                break;

            case 'boolean':
                $result = $value ? 'true' : 'false';
                break;

            case 'NULL':
                $result = 'null';
                break;

            case 'integer':
            case 'double':
                $result = (string)$value;
                break;

            case 'string':
                $enquote = false;
                switch ($value) {
                    case 'true':
                    case 'false':
                    case 'null':
                        $enquote = true;
                        break;

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
    protected function unserialize($value)
    {
        $result = json_decode($value, true);
        if (is_null($result) && trim(strtolower($value)) !== 'null') {
            return (string)$value;
        }

        return $result;
    }
}
