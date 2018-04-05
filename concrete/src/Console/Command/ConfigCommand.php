<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Config\DirectFileSaver;
use Concrete\Core\Config\FileSaver;
use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\Repository\Repository;
use Illuminate\Filesystem\Filesystem;
use Concrete\Core\Console\Command;
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

    /**
     * @var Repository
     */
    protected $repository;

    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:config')
            ->setDescription('Set or get configuration parameters.')
            ->addArgument('operation', InputArgument::REQUIRED, 'The operation to accomplish (' . implode('|', $this->getAllowedOperations()) . ')')
            ->addArgument('item', InputArgument::REQUIRED, 'The configuration item (eg: concrete.debug.display_errors)')
            ->addArgument('value', InputArgument::OPTIONAL, 'The new value of the configuration item')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addOption('environment', 'e', InputOption::VALUE_REQUIRED, 'The environment (if not specified, we\'ll work with the configuration item valid for all environments)')
            ->addOption('generated-overrides', 'g', InputOption::VALUE_NONE, 'Set this option to save configurations to the generated_overrides folder')
        ;
        $this->setHelp(<<<EOT
When setting values that may be evaluated as boolean (true/false), null or numbers, but you want to store them as strings, you can enclose those values in single or double quotes.
For instance, with
concrete5 %command.name% set concrete.test_item 1
The new configuration item will have a numeric value of 1. If you want to save the string "1" you have to write
concrete5 %command.name% set concrete.test_item '1'

Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-config
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $default_environment = \Config::getEnvironment();

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
        switch ($input->getArgument('operation')) {
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
                throw new Exception('Invalid operation specified. Allowed operations: ' . implode(', ', $this->getAllowedOperations()));
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
                $result = (string) $value;
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
            return (string) $value;
        }

        return $result;
    }
}
