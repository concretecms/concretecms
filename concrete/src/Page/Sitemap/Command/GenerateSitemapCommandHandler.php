<?php

namespace Concrete\Core\Page\Sitemap\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\SitemapWriter;

class GenerateSitemapCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var SitemapWriter
     */
    protected $writer;

    /**
     * GenerateSitemapCommandHandler constructor.
     * @param SitemapWriter $writer
     */
    public function __construct(SitemapWriter $writer)
    {
        $this->writer = $writer;
    }

    public function __invoke(GenerateSitemapCommand $command)
    {
        $numPages = 0;
        $this->writer->generate(function (SitemapElement $data) use (&$numPages) {
            if ($data instanceof SitemapPage) {
                ++$numPages;
            }
        });
        $sitemapUrl = $this->writer->getSitemapUrl();
        $this->output->write(t('Sitemap URL available at: %s', $sitemapUrl));
    }
    
}
