<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSitemapCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:sitemap:generate')
            ->setDescription('Generate the sitemap in XML format.')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addOption('site', 's', InputOption::VALUE_REQUIRED, 'The handle of the site')
            ->addOption('url', 'u', InputOption::VALUE_REQUIRED, 'The canonical URL of the site (required if no canonical URL is defined for the site).')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'The full path of the file where the sitemap will be saved')
        ;
        $this->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  {$errExitCode} errors occurred
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        $writer = $app->make(SitemapWriter::class);
        $generator = $writer->getSitemapGenerator();
        $pageListGenerator = $generator->getPageListGenerator();
        $siteHandle = (string) $input->getOption('site');
        if ($siteHandle !== '') {
            $site = $app->make('site')->getByHandle($siteHandle);
            if ($site === null) {
                throw new UserMessageException(sprintf('No site with handle "%s" has been found.', $siteHandle));
            }
            $pageListGenerator->setSite($site);
        } else {
            $site = $pageListGenerator->getSite();
        }
        $customCanonicalUrl = (string) $input->getOption('url');
        if ($customCanonicalUrl === '') {
            $canonicalUrl = $generator->getSiteCanonicalUrl();
            if ($canonicalUrl === '') {
                throw new UserMessageException('The site does not define a canonical URL: you must specify the --url option.');
            }
        } else {
            $generator->setCustomSiteCanonicalUrl($customCanonicalUrl);
        }
        $outputFilename = (string) $input->getOption('output');
        if ($outputFilename !== '') {
            $writer->setOutputFilename($outputFilename);
        } else {
            $outputFilename = $writer->getOutputFilename();
        }
        $progressBar = new ProgressBar($output, $pageListGenerator->getApproximatePageCount());
        $progressBar->setMessage('Adding pages to sitemap');
        $progressBar->display();
        $numPages = 0;
        $writer->generate(function (SitemapElement $element) use ($progressBar, &$numPages) {
            if ($element instanceof SitemapPage) {
                $progressBar->advance();
                ++$numPages;
            }
        });
        $progressBar->clear();
        $output->writeln('');
        $output->writeln(sprintf('Sitemap generated at: %s', str_replace('/', DIRECTORY_SEPARATOR, $outputFilename)));
        $sitemapUrl = $writer->getSitemapUrl();
        if ($sitemapUrl !== '') {
            $output->writeln(sprintf('Sitemap visible at: %s', $sitemapUrl));
        }
        $output->writeln(sprintf('Number of pages included in sitemap: %s', $numPages));
    }
}
