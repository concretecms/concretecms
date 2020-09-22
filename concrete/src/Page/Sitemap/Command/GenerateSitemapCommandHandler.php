<?php

namespace Concrete\Core\Page\Sitemap\Command;

use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\SitemapWriter;

class GenerateSitemapCommandHandler
{

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

    public function handle(GenerateSitemapCommand $command)
    {
        $numPages = 0;
        $this->writer->generate(function (SitemapElement $data) use (&$numPages) {
            if ($data instanceof SitemapPage) {
                ++$numPages;
            }
        });
        $sitemapUrl = $this->writer->getSitemapUrl();
    }
    
}
