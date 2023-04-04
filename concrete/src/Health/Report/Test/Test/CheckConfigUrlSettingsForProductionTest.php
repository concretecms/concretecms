<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Health\Report\Finding\Control\Location\UrlSettingsLocation;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;
use Concrete\Core\Site\Service;
use Concrete\Core\Url\Url;

class CheckConfigUrlSettingsForProductionTest implements TestInterface
{

    /**
     * @var Service
     */
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function run(Runner $report): void
    {
        $site = $this->service->getDefault();
        $url = $site->getSiteCanonicalURL();

        if (!$url) {
            $report->alert(
                t('No canonical URL set! You ought to set canonical URLs for all sites running in production.'),
                $report->button(new UrlSettingsLocation())
            );
        } else {
            $url = Url::createFromUrl($url);
            if ((string) $url->getScheme() !== 'https') {
                $report->warning(
                    t('Canonical URL set but not running SSL. SSL is strongly encouraged.'),
                    $report->button(new UrlSettingsLocation())
                );
            }

        }
	}

}
