<?php

namespace Concrete\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Job as AbstractJob;

class GenerateSitemap extends AbstractJob implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

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
        $generator = $writer->getSitemapGenerator();
        if ($generator->getSiteCanonicalUrl() === '') {
            $request = $this->app->make(Request::class);
            if (!$request->getHost()) {
                throw new UserMessageException(t('Canonical URL is not set and no HTTP request to retrieve the site URL from.'));
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
}
