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

class ActivateThemeSkinCommand extends Command
{
    protected function configure()
    {
        $this->setName('concrete:theme:activate-skin')
            ->addOption(
                'site',
                's',
                InputOption::VALUE_OPTIONAL,
                'Apply the theme skin to a site. If omitted will use the default.',
                null
            )
            ->setDescription('Activate a theme skin.')
            ->addArgument('skin', InputArgument::REQUIRED, 'The name of the skin.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $skinIdentifier = (string) $input->getArgument('skin');
        $app = Application::getFacadeApplication();
        $service = $app->make(Service::class);
        $entityManager = $app->make(EntityManager::class);

        if ($input->getOption('site')) {
            $site = $service->getByHandle((string) $input->getOption('site'));
        } else {
            $site = $service->getDefault();
        }
        if (!$site) {
            $output->writeln('<fg=red>' . t('Invalid site.') . '</>');
            return static::FAILURE;
        }

        $theme = Theme::getByID($site->getThemeID());
        if (!$theme) {
            $output->writeln('<fg=red>' . t('Site has no active theme!') . '</>');
            return static::FAILURE;
        }

        $output->writeln(t('Active theme for site is "%s"', $theme->getThemeHandle()));
        $skin = $theme->getSkinByIdentifier((string) $skinIdentifier);
        if (!$skin) {
            $availableSkinIdentifiers = [];
            $availableSkins = $theme->getSkins();
            foreach ($availableSkins as $availableSkin) {
                $availableSkinIdentifiers[] = $availableSkin->getIdentifier();
            }
            $output->writeln('<fg=red>' . t('There is no skin named "%s" found in this theme. Active skins include: %s.', $skinIdentifier, implode(', ', $availableSkinIdentifiers)) . '</>');
            return static::FAILURE;
        }

        $output->writeln(t('Activating skin "%s"', $skinIdentifier));
        $site->setThemeSkinIdentifier((string) $skinIdentifier);
        $entityManager->persist($site);
        $entityManager->flush();
        $output->writeln('<fg=green>' . t('Theme skin activation complete!') . '</>');
        return static::SUCCESS;
    }
}
