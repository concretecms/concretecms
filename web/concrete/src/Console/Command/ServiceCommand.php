<?php
namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Concrete\Core\Service\ServiceInterface;
use Core;
use Exception;

final class ServiceCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:service')
            ->setDescription('Check or update the web server configuration')
            ->addOption('service-version', 'r', InputOption::VALUE_REQUIRED, 'The specific version of the web server software', '')
            ->addArgument('service', InputArgument::REQUIRED, 'The web server to use (apache|nginx)')
            ->addArgument('operation', InputArgument::REQUIRED, 'The operation to perform (check|update)')
            ->setHelp(<<<EOT
Return codes for the check operation:
  0 operation completed successfully
  1 errors occurred
  2 web server configuration is not aligned

Return codes for the update operation:
  0 operation completed successfully
  1 errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $manager = Core::make('Concrete\Core\Service\Manager\ServiceManager');
            /* @var \Concrete\Core\Service\Manager\ServiceManager $manager */
            $service = $manager->getService($input->getArgument('service'), $input->getOption('service-version'));
            if ($service === null) {
                $msg = 'Unknown web server handle: '.$input->getArgument('service');
                $msg .= PHP_EOL;
                $msg .= 'Valid handles: '.implode($manager->getExtensions());
                throw new Exception($msg);
            }
            $operation = $input->getArgument('operation');
            switch ($operation) {
                case 'check':
                    if ($this->checkConfiguration($service, $output) === false) {
                        $rc = 2;
                    }
                    break;
                case 'update':
                    $this->updateConfiguration($service, $output);
                    break;
                default:
                    throw new Exception('Invalid value of the operation argument (valid values: check or update');
            }
        } catch (Exception $x) {
            $output->writeln('<error>'.$x->getMessage().'</error>');
            $rc = 1;
        }

        return $rc;
    }

    public function checkConfiguration(ServiceInterface $service, OutputInterface $output)
    {
        $storage = $service->getStorage();
        if (!$storage->canRead()) {
            throw new Exception('Unable to read current server configuration for '.$service->getFullName());
        }
        $configuration = $storage->read();
        $generator = $service->getGenerator();
        $configurator = $service->getConfigurator();
        $allOk = true;
        foreach ($generator->getRules() as $ruleHandle => $rule) {
            $shouldHave = $generator->ruleShouldBeEnabled($ruleHandle);
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

    public function updateConfiguration(ServiceInterface $service, OutputInterface $output)
    {
        $storage = $service->getStorage();
        if (!$storage->canRead()) {
            throw new Exception('Unable to read the current server configuration for '.$service->getFullName());
        }
        $configuration = $storage->read();
        $generator = $service->getGenerator();
        $configurator = $service->getConfigurator();
        $configurationUpdated = false;
        foreach ($generator->getRules() as $ruleHandle => $rule) {
            $shouldHave = $generator->ruleShouldBeEnabled($ruleHandle);
            if ($shouldHave === true) {
                $output->write("Checking presence of rule $ruleHandle... ");
                if ($configurator->hasRule($configuration, $rule)) {
                    $output->writeln("<info>already present.</info>");
                } else {
                    $output->write("not found. Adding it... ");
                    $configuration = $configurator->addRule($configuration, $rule);
                    $output->writeln("<info>done.</info>");
                    $configurationUpdated = true;
                }
            } elseif ($shouldHave === false) {
                $output->write("Checking absence of rule $ruleHandle... ");
                if ($configurator->hasRule($configuration, $rule)) {
                    $output->write("found. Removing it... ");
                    $configuration = $configurator->removeRule($configuration, $rule);
                    $output->writeln("<info>done.</info>");
                    $configurationUpdated = true;
                } else {
                    $output->writeln("<info>already absent.</info>");
                }
            }
        }
        if ($configurationUpdated) {
            $output->write("Persisting new configuration... ");
            if (!$storage->canWrite()) {
                throw new Exception('Unable to write the current server configuration for '.$service->getFullName());
            }
            $storage->write($configuration);
            $output->writeln("<info>done.</info>");
        }
    }
}
