<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Concrete\Core\Service\ServiceInterface;
use Concrete\Core\Service\Rule\RuleInterface;
use Concrete\Core\Service\Rule\ConfigurableRuleInterface;
use Core;
use Exception;

final class ServiceCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $serviceHandles = [];
        $help = '';
        $manager = Core::make('Concrete\Core\Service\Manager\ServiceManager');
        /* @var \Concrete\Core\Service\Manager\ServiceManager $manager */
        foreach ($manager->getAllServices() as $serviceHandle => $service) {
            $serviceHandles[] = $serviceHandle;
            foreach ($service->getGenerator()->getRules() as $ruleHandle => $rule) {
                if ($rule instanceof ConfigurableRuleInterface) {
                    /* @var ConfigurableRuleInterface $rule */
                    foreach ($rule->getOptions() as $optionHandle => $option) {
                        $help .= "Rule option for service $serviceHandle, rule $ruleHandle:\n";
                        $help .= "  - $optionHandle: " . $option->getDescription();
                        if ($option->isRequired()) {
                            $help .= ' [required]';
                        } else {
                            $help .= ' [optional]';
                        }
                        $help .= "\n";
                    }
                }
            }
        }
        $help .= <<<EOT

Return codes for the check operation:
  0 operation completed successfully
  $errExitCode errors occurred
  2 web server configuration is not aligned

Return codes for the update operation:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-service
EOT
        ;
        $this
            ->setName('c5:service')
            ->setDescription('Check or update the web server configuration')
            ->addEnvOption()
            ->addOption('service-version', 'r', InputOption::VALUE_REQUIRED, 'The specific version of the web server software', '')
            ->addArgument('service', InputArgument::REQUIRED, 'The web server to use (' . implode('|', $serviceHandles) . ')')
            ->addArgument('operation', InputArgument::REQUIRED, 'The operation to perform (check|update)')
            ->addArgument('rule-options', InputArgument::IS_ARRAY, 'List of key-value pairs to pass to the rules (example: foo=bar baz=foo)')
            ->setHelp(trim($help))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        $manager = Core::make('Concrete\Core\Service\Manager\ServiceManager');
        /* @var \Concrete\Core\Service\Manager\ServiceManager $manager */
        $service = $manager->getService($input->getArgument('service'), $input->getOption('service-version'));
        if ($service === null) {
            $msg = 'Unknown web server handle: ' . $input->getArgument('service');
            $msg .= PHP_EOL;
            $msg .= 'Valid handles: ' . implode(', ', $manager->getExtensions());
            throw new Exception($msg);
        }
        $operation = $input->getArgument('operation');
        $ruleOptions = $this->parseRuleOptions($input);
        switch ($operation) {
            case 'check':
                if ($this->checkConfiguration($service, $ruleOptions, $output) === false) {
                    $rc = 2;
                }
                break;
            case 'update':
                $this->updateConfiguration($service, $ruleOptions, $output);
                break;
            default:
                throw new Exception('Invalid value of the operation argument (valid values: check or update');
        }

        return $rc;
    }

    public function checkConfiguration(ServiceInterface $service, array $ruleOptions, OutputInterface $output)
    {
        $storage = $service->getStorage();
        if (!$storage->canRead()) {
            throw new Exception('Unable to read current server configuration for ' . $service->getFullName());
        }
        $configuration = $storage->read();
        $generator = $service->getGenerator();
        $configurator = $service->getConfigurator();
        $allOk = true;
        foreach ($generator->getRules() as $ruleHandle => $rule) {
            $this->configureRule($rule, $ruleOptions);
            $shouldHave = $rule->isEnabled();
            if ($shouldHave !== null) {
                if ($shouldHave) {
                    $output->write("Checking presence of rule $ruleHandle... ");
                    if ($configurator->hasRule($configuration, $rule)) {
                        $output->writeln('<info>found (ok).</info>');
                    } else {
                        $output->writeln('<error>NOT FOUND!</error>');
                    }
                } else {
                    $output->write("Checking absence of rule $ruleHandle... ");
                    if ($configurator->hasRule($configuration, $rule)) {
                        $output->writeln('<error>FOUND!</error>');
                    } else {
                        $output->writeln('<info>not found (ok).</info>');
                    }
                }
            }
        }

        return $allOk;
    }

    public function updateConfiguration(ServiceInterface $service, array $ruleOptions, OutputInterface $output)
    {
        // Initialize some variable
        $storage = $service->getStorage();
        if (!$storage->canRead()) {
            throw new Exception('Unable to read the current server configuration for ' . $service->getFullName());
        }
        $configuration = $storage->read();
        $generator = $service->getGenerator();
        $configurator = $service->getConfigurator();
        $configurationUpdated = false;

        // Let's check every defined rule
        foreach ($generator->getRules() as $ruleHandle => $rule) {
            $this->configureRule($rule, $ruleOptions);

            // Let's see if this rule should be present or not in the configuration
            $shouldHave = $rule->isEnabled();

            if ($shouldHave === true) {
                // This rule should be present in the configuration
                $output->write("Checking presence of rule $ruleHandle... ");
                if ($configurator->hasRule($configuration, $rule)) {
                    // The rule is already in the configuration
                    $output->writeln("<info>already present.</info>");
                } else {
                    // The rule is not in the configuration: let's add it
                    $output->write("not found. Adding it... ");
                    $configuration = $configurator->addRule($configuration, $rule);
                    $output->writeln("<info>done.</info>");
                    $configurationUpdated = true;
                }
            } elseif ($shouldHave === false) {
                // This rule should not be present in the configuration
                $output->write("Checking absence of rule $ruleHandle... ");
                if ($configurator->hasRule($configuration, $rule)) {
                    // The rule is in the configuration: let's remove it
                    $output->write("found. Removing it... ");
                    $configuration = $configurator->removeRule($configuration, $rule);
                    $output->writeln("<info>done.</info>");
                    $configurationUpdated = true;
                } else {
                    // The rule is not in the configuration
                    $output->writeln("<info>already absent.</info>");
                }
            }
        }

        if ($configurationUpdated) {
            // Some rule has been added or removed from the configuration: let's save it
            $output->write("Persisting new configuration... ");
            if (!$storage->canWrite()) {
                throw new Exception('Unable to write the current server configuration for ' . $service->getFullName());
            }
            $storage->write($configuration);
            $output->writeln("<info>done.</info>");
        }
    }

    /**
     * Parse the rule-options input argument.
     *
     * @param InputInterface $input
     *
     * @throws Exception
     *
     * @return array
     */
    protected function parseRuleOptions(InputInterface $input)
    {
        $ruleOptions = [];
        foreach ($input->getArgument('rule-options') as $keyValuePair) {
            list($key, $value) = explode('=', $keyValuePair, 2);
            $key = trim($key);
            if (substr($key, -2) === '[]') {
                $isArray = true;
                $key = rtrim(substr($key, 0, -2));
            } else {
                $isArray = false;
            }
            if ($key === '' || !isset($value)) {
                throw new Exception(sprintf("Unable to parse the rule option '%s': it must be in the form of key=value", $keyValuePair));
            }
            if (isset($ruleOptions[$key])) {
                if (!($isArray && is_array($ruleOptions[$key]))) {
                    throw new Exception(sprintf("Duplicated rule option '%s'", $key));
                }
                $ruleOptions[$key][] = $value;
            } else {
                $ruleOptions[$key] = $isArray ? ((array) $value) : $value;
            }
        }

        return $ruleOptions;
    }

    protected function configureRule(RuleInterface $rule, array $options)
    {
        if ($rule instanceof ConfigurableRuleInterface) {
            foreach ($rule->getOptions() as $optionHandle => $optionValue) {
                if (isset($options[$optionHandle])) {
                    $optionValue->setValue($options[$optionHandle]);
                }
            }
        }
    }
}
