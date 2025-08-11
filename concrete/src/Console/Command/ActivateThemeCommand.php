<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateThemeCommand extends Command
{
    protected function configure()
    {
        $this->setName('concrete:theme:activate')
            ->addOption(
                'site',
                's',
                InputOption::VALUE_OPTIONAL,
                'Apply the theme skin to a site. If omitted will use the default.',
                null
            )
            ->setDescription('Activate a theme.')
            ->addArgument('theme', InputArgument::REQUIRED, 'The name of the theme.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themeIdentifier = (string) $input->getArgument('theme');
        $app = Application::getFacadeApplication();
        $service = $app->make(Service::class);

        if ($input->getOption('site')) {
            $site = $service->getByHandle((string) $input->getOption('site'));
        } else {
            $site = $service->getDefault();
        }
        if (!$site) {
            $output->writeln('<fg=red>' . t('Invalid site.') . '</>');
            return static::FAILURE;
        }

        $theme = Theme::getByHandle($themeIdentifier);
        if (!$theme) {
            $availableThemeIdentifiers = [];
            $availableThemes = Theme::getList();
            foreach ($availableThemes as $availableTheme) {
                $availableThemeIdentifiers[] = $availableTheme->getThemeHandle();
            }
            $output->writeln('<fg=red>' . t('No theme found with the handle "%s"! Available themes include: %s.', $themeIdentifier, implode(', ', $availableThemeIdentifiers)) . '</>');
            return static::FAILURE;
        }

        $output->writeln(t('Activating theme "%s"', $themeIdentifier));
        $theme->applyToSite($site);

        $output->writeln('<fg=green>' . t('Theme activation complete!') . '</>');
        return static::SUCCESS;
    }
}
