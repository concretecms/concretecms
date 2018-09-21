<?php

namespace Concrete\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Sitemap\DeprecatedPageListGenerator;
use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\PageListGenerator;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Job as AbstractJob;
use RuntimeException;

/**
 * Usage of this class for CLI has been deprecated: use the c5:sitemap:generate CLI command instead, or the classes in the Concrete\Core\Page\Sitemap namespace.
 *
 * @method static bool canIncludePageInSitemap($page, $instances) Deprecated: use the new classes in the Concrete\Core\Page\Sitemap namespace
 */
class GenerateSitemap extends AbstractJob implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @deprecated Use the line terminator feature of the SitemapWriter class
     */
    const EOL = "\n";

    /**
     * @deprecated
     *
     * @param mixed $name
     * @param mixed $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        if (strcasecmp($name, 'canIncludePageInSitemap') === 0) {
            return self::_canIncludePageInSitemap($arguments[0], $arguments[1]);
        }
        throw new RuntimeException(t('Method %1$s does not exist for %2$s class.', $name, __CLASS__));
    }

    /**
     * @deprecated
     *
     * @param mixed $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments)
    {
        if (strcasecmp($name, 'canIncludePageInSitemap') === 0) {
            return self::_canIncludePageInSitemap($arguments[0], $arguments[1]);
        }
        throw new RuntimeException(t('Method %1$s does not exist for %2$s class.', $name, __CLASS__));
    }

    /**
     * Returns the job name.
     *
     * @return string
     */
    public function getJobName()
    {
        return t('Generate the sitemap.xml file');
    }

    /**
     * Returns the job description.
     *
     * @return string
     */
    public function getJobDescription()
    {
        return t('Generate the sitemap.xml file that search engines use to crawl your site.');
    }

    /**
     * Executes the job.
     *
     * @throws \Exception throws an exception in case of errors
     *
     * @return string returns a string describing the job result in case of success
     */
    public function run()
    {
        $writer = $this->app->make(SitemapWriter::class);
        if (method_exists($this, 'canIncludePageInSitemap')) {
            // Add support for deprecated custom canIncludePageInSitemap
            $pageListGenerator = $this->app->make(DeprecatedPageListGenerator::class);
            $pageListGenerator->setDeprecatedChecker(function ($page, $instances) {
                return $this->canIncludePageInSitemap($page, $instances);
            });
            $writer->getSitemapGenerator()->setPageListGenerator($pageListGenerator);
        }
        $generator = $writer->getSitemapGenerator();
        if ($generator->getSiteCanonicalUrl() === '') {
            $request = $this->app->make(Request::class);
            if (!$request->getHost()) {
                throw new UserMessageException(t('Canonical URL is not set and there is no HTTP request to retrieve the site URL from.'));
            }
            $generator->setCustomSiteCanonicalUrl($request->getSchemeAndHttpHost());
        }
        $numPages = 0;
        $writer->generate(function (SitemapElement $data) use (&$numPages) {
            if ($data instanceof SitemapPage) {
                ++$numPages;
            }
        });
        $sitemapUrl = $writer->getSitemapUrl();
        if ($sitemapUrl === '') {
            $openLink = '';
        } else {
            $openLink = sprintf('<a href="%s" target="_blank">', h($sitemapUrl));
        }

        return t2(
            /*i18n: %2$s and %3$s contains HTML markup, %1$d is the number of pages */
            'The %2$ssitemap%3$s has been generated (%1$d page)',
            'The %2$ssitemap%3$s has been generated (%1$d pages)',
            $numPages,
            $openLink,
            $openLink === '' ? '' : '</a>'
        );
    }

    /**
     * @deprecated BC implementation of deprecated canIncludePageInSitemap method: use the new classes in the Concrete\Core\Page\Sitemap namespace
     *
     * @param \Concrete\Core\Page\Page $page
     * @param array $instances
     */
    private static function _canIncludePageInSitemap($page, $instances)
    {
        static $generator;
        if ($generator === null) {
            $generator = \Core::make(PageListGenerator::class);
        }

        return $generator->canIncludePageInSitemap($page);
    }
}
