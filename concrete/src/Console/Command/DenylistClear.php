<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\IpAccessControlService;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Punic\Comparer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DenylistClear extends Command
{
    /**
     * Value for the '--automatic-bans' option: all bans.
     *
     * @var string
     */
    const DELETE_AUTOMATIC_BANS_ALL = 'all';

    /**
     * Value for the '--automatic-bans' option: expired bans.
     *
     * @var string
     */
    const DELETE_AUTOMATIC_BANS_EXPIRED = 'expired';

    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $automaticBansAll = static::DELETE_AUTOMATIC_BANS_ALL;
        $automaticBansExpired = static::DELETE_AUTOMATIC_BANS_EXPIRED;
        $this
            ->setName('c5:denylist:clear')
            ->setDescription('Clear denylist-related data')
            ->addArgument('handle', InputArgument::IS_ARRAY, 'List of IP Access Control Category handles (if not specified: apply to all the categories)')
            ->addEnvOption()
            ->addOption('min-age', 'm', InputOption::VALUE_REQUIRED, 'Clear events older that this number of seconds (0 for all)')
            ->addOption('automatic-bans', 'b', InputOption::VALUE_REQUIRED, "Clear automatic bans (\"{$automaticBansExpired}\" to only delete expired bans, \"{$automaticBansAll}\" to delete the current bans too)")
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List the available IP Access Control Category handles')
            ->addOption('failed-login-age', 'f', InputOption::VALUE_REQUIRED, '*DEPRECATED* use --min-age')
            ->setHelp(<<<EOT
You can use this command to clear the data related to IP address denylist.

To clear the events data, use the --min-age option.
To clear the automatic bans, use the --automatic-bans option.

Returns codes:
  $okExitCode operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-denylist-clear
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('list')) {
            $this->listCategories($output);

            return static::SUCCESS;
        }
        $app = Application::getFacadeApplication();
        $minAge = $input->getOption('min-age');
        if ($minAge === null) {
            $minAge = $input->getOption('failed-login-age');
        }
        if ($minAge !== null) {
            $valn = $app->make('helper/validation/numbers');
            if (!$valn->integer($minAge, 0)) {
                throw new UserMessageException('Invalid value of the --min-age option: please specify a non-negative integer.');
            }
            $minAge = (int) $minAge;
        }
        $automaticBans = $input->getOption('automatic-bans');
        if ($automaticBans !== null) {
            switch ($automaticBans) {
                case static::DELETE_AUTOMATIC_BANS_ALL:
                case static::DELETE_AUTOMATIC_BANS_EXPIRED:
                    break;
                default:
                    throw new UserMessageException('Invalid value of the --automatic-bans option: valid values are "expired" and "all".');
            }
        }
        if ($minAge === null && $automaticBans === null) {
            throw new UserMessageException('Please specify at least one of the options --min-age option or --automatic-bans');
        }
        $repo = $app->make(EntityManagerInterface::class)->getRepository(IpAccessControlCategory::class);
        $handles = $input->getArgument('handle');
        if ($handles === []) {
            $categories = $repo->findAll();
        } else {
            $categories = [];
            foreach ($input->getArgument('handle') as $handle) {
                $category = $repo->findOneBy(['handle' => $handle]);
                if ($category === null) {
                    throw new UserMessageException(sprintf('Unknown IP Access Control Category handle: "%s"', $handle));
                }
                if (!in_array($category, $categories, true)) {
                    $categories[] = $category;
                }
            }
        }
        if (empty($categories)) {
            throw new UserMessageException('No IP Access Control Category found');
        }
        foreach ($categories as $category) {
            $output->writeln("# {$category->getName()}");
            $service = $app->make(IpAccessControlService::class, ['category' => $category]);
            if ($minAge !== null) {
                if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                    $output->write("Clearing events older than {$minAge} seconds... ");
                }
                $count = $service->deleteEvents($minAge);
                if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                    $output->writeln("{$count} records deleted.");
                }
            }
            if ($automaticBans !== null) {
                if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                    switch ($automaticBans) {
                        case static::DELETE_AUTOMATIC_BANS_ALL:
                            $output->write('Deleting all the automatic bans... ');
                            break;
                        case static::DELETE_AUTOMATIC_BANS_EXPIRED:
                            $output->write('Deleting the expired automatic bans... ');
                            break;
                    }
                }
                switch ($automaticBans) {
                    case static::DELETE_AUTOMATIC_BANS_ALL:
                        $count = $service->deleteAutomaticDenylist(false);
                        break;
                    case static::DELETE_AUTOMATIC_BANS_EXPIRED:
                        $count = $service->deleteAutomaticDenylist(true);
                        break;
                }
                if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                    $output->writeln("{$count} records deleted.");
                }
            }
        }

        return static::SUCCESS;
    }

    protected function listCategories(OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        $repo = $app->make(EntityManagerInterface::class)->getRepository(IpAccessControlCategory::class);
        $categories = $repo->findAll();
        $cmp = new Comparer();
        usort($categories, function (IpAccessControlCategory $a, IpAccessControlCategory $b) use ($cmp) {
            return $cmp->compare($a->getDisplayName(), $b->getDisplayName());
        });
        $table = new Table($output);
        $table->setHeaders(['Handle', 'Name', 'Enabled']);
        foreach ($categories as $category) {
            $table->addRow([$category->getHandle(), $category->getName(), $category->isEnabled() ? 'Yes' : 'No']);
        }
        $table->render();
    }
}
