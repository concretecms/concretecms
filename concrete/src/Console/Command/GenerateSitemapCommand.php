<?php

declare(strict_types=1);

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

defined('C5_EXECUTE') or die('Access Denied.');

class GenerateSitemapCommand extends Command
{
    /**
     * Configure the command name, description, and options.
     *
     * Options:
     *   --site / -s   Handle of the site to generate for (recommended; omitting is deprecated).
     *   --url  / -u   Override canonical URL, required when the site has none configured.
     *   --output / -o Full filesystem path for the output file; defaults to the per-site filename.
     */
    protected function configure(): void
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $this
            ->setName('c5:sitemap:generate')
            ->setDescription('Generate the sitemap in XML format.')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addOption('site', 's', InputOption::VALUE_REQUIRED, 'The handle of the site (recommended; omitting is deprecated)')
            ->addOption('url', 'u', InputOption::VALUE_REQUIRED, 'The canonical URL of the site (required if no canonical URL is defined for the site).')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'The full path of the file where the sitemap will be saved')
        ;
        $this->setHelp(
            <<<EOT
            Returns codes:
              {$okExitCode} operation completed successfully
              {$errExitCode} errors occurred

            Omitting --site is deprecated. Always pass --site to target a specific site.
            EOT
        );
    }

    /**
     * Generate the sitemap.
     *
     * When --site is given, delegates to SitemapWriter::generateForSite() which writes
     * `sitemap-<handle>.xml` and scopes page traversal to that site.
     *
     * When --site is omitted (deprecated), resolves the active site and writes to the
     * legacy `sitemap.xml` filename for backward compatibility.
     *
     * @throws \Concrete\Core\Error\UserMessageException if the site handle is unknown,
     *                                                   no canonical URL can be determined, or no active site can be resolved
     * @return int exit code (SUCCESS or FAILURE)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $app = Application::getFacadeApplication();
        $writer = $app->make(SitemapWriter::class);
        $pageListGenerator = $writer->getSitemapGenerator()->getPageListGenerator();
        $siteHandle = (string) $input->getOption('site');
        $customCanonicalUrl = (string) $input->getOption('url');
        $customOutputFilename = (string) $input->getOption('output');

        if ($customOutputFilename !== '') {
            $writer->setOutputFilename($customOutputFilename);
        }

        $progressBar = new ProgressBar($output, $pageListGenerator->getApproximatePageCount());
        $progressBar->setMessage('Adding pages to sitemap');
        $progressBar->display();
        $numPages = 0;
        $pulse = static function (SitemapElement $element) use ($progressBar, &$numPages) {
            if ($element instanceof SitemapPage) {
                $progressBar->advance();
                $numPages++;
            }
        };

        if ($siteHandle !== '') {
            $site = $app->make('site')->getByHandle($siteHandle);
            if ($site === null) {
                throw new UserMessageException(sprintf('No site with handle "%s" has been found.', $siteHandle));
            }
            if ($customCanonicalUrl === '' && $site->getSiteCanonicalURL() === '') {
                throw new UserMessageException('The site does not define a canonical URL: you must specify the --url option.');
            }
            $writer->generateForSite($site, $customCanonicalUrl, $pulse);
            $outputFilename = $customOutputFilename !== '' ? $customOutputFilename : SitemapWriter::getOutputFilenameForSite($site);
            $sitemapUrl = $writer->getSitemapUrlForSite($site);
        } else {
            // No --site given: deprecated — use --site to target a specific site explicitly.
            // Generates for the active site using the legacy sitemap.xml filename.
            $site = $app->make('site')->getSite();
            if ($site === null) {
                throw new UserMessageException('Could not determine the active site. Use --site to specify one explicitly.');
            }
            if ($customCanonicalUrl === '' && $site->getSiteCanonicalURL() === '') {
                throw new UserMessageException('The site does not define a canonical URL: you must specify the --url option.');
            }
            // Pre-set the resolved legacy filename so generateForSite() writes there instead of
            // sitemap-<handle>.xml, and the finally block restores it correctly.
            if ($customOutputFilename === '') {
                $writer->setOutputFilename($writer->getOutputFilename());
            }
            $outputFilename = $writer->getOutputFilename();
            $writer->generateForSite($site, $customCanonicalUrl, $pulse);
            // generateForSite() restores generator state, so compute the URL directly.
            $effectiveCanonical = $customCanonicalUrl !== '' ? $customCanonicalUrl : $site->getSiteCanonicalURL();
            $sitemapUrl = $effectiveCanonical !== '' && str_starts_with($outputFilename, DIR_BASE . '/')
                ? rtrim($effectiveCanonical, '/') . substr($outputFilename, strlen(DIR_BASE))
                : '';
        }

        $progressBar->clear();
        $output->writeln('');
        $output->writeln(sprintf('Sitemap generated at: %s', str_replace('/', DIRECTORY_SEPARATOR, $outputFilename)));
        if ($sitemapUrl !== '') {
            $output->writeln(sprintf('Sitemap visible at: %s', $sitemapUrl));
        }
        $output->writeln(sprintf('Number of pages included in sitemap: %s', $numPages));

        return static::SUCCESS;
    }
}
