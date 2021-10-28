<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Page\Theme\Theme;
use Loader;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallThemeCommand extends Command
{
    protected function configure()
    {
        $this->setName('c5:theme:install')
        ->addOption('activate', 'a', InputOption::VALUE_NONE, 'Activate this theme after install', null)
        ->setDescription('Install a Concrete Theme')
        ->setCanRunAsRoot(false)
        ->addArgument('theme-handle', null, InputOption::VALUE_REQUIRED, 'The handle name of the theme');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = static::SUCCESS;
        $theme = Theme::getByFileHandle($input->getArgument('theme-handle'));

        $app = Application::getFacadeApplication();
        $config = $app->make('config');

        $v = Loader::helper('validation/error');
        try {
            if (is_object($theme)) {
                $theme = Theme::add($input->getArgument('theme-handle'));
                $output->writeln('<info>Theme Installed successfully!</info>');
                if ($input->getOption('activate')) {
                    $theme->applyToSite();
                }
            } else {
                throw new Exception('Invalid Theme');
            }
        } catch (Exception $e) {
            switch ($e->getMessage()) {
                case Theme::E_THEME_INSTALLED:
                    $output->writeln('That theme has already been installed.');
                    break;
                default:
                    $output->writeln($e->getMessage());
                    $rc = static::FAILURE;
                    break;
            }
        }

        return $rc;
    }
}
