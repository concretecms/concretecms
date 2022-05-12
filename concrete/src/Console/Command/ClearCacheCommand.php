<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Core;
use Exception;

class ClearCacheCommand extends Command
{
    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $this
            ->setName('c5:clear-cache')
            ->setDescription('Clear the cache')
            ->addOption('thumbnails', 't', InputOption::VALUE_REQUIRED, "Should the thumbnails be removed from the cache? [Y/N]")
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->setHelp(<<<EOT
If the --thumbnails options is not specified, we'll use the last value set in the dashboard.

Returns codes:
  $okExitCode operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-clear-cache
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cms = Core::make('app');
        $thumbnails = $input->getOption('thumbnails');
        $config = $cms->app->make(Repository::class);
        if ($thumbnails !== null) {
            switch (strtolower($thumbnails[0])) {
                case 'n':
                    $clearThumbnails = false;
                    break;
                case 'y':
                    $clearThumbnails = true;
                    break;
                default:
                    throw new Exception('Invalid value for the --thumbnails option: please specify Y[es] or N[o]');
            }
            $config->set('concrete.cache.clear.thumbnails', $clearThumbnails);
        }
        $output->write('Clearing the cache... ');
        $cms->clearCaches();
        $config->set('concrete.cache.last_cleared', time());
        $output->writeln('<info>done.</info>');

        return static::SUCCESS;
    }
}
