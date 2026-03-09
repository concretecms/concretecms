<?php
namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Sitemap\SitemapWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class Sitemap extends Controller
{
    public function view()
    {
        try {
            $sitemapXML = $this->app->make(SitemapWriter::class)->getOutputFilename();

            return new BinaryFileResponse($sitemapXML);
        } catch (FileNotFoundException $e) {
            return $this->app->make(ResponseFactoryInterface::class)->notFound('');
        }
    }
}