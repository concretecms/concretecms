<?php

namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Controls\Location\CacheSettingsLocation;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigCacheSettingsForProductionTest implements TestInterface
{

    protected $config;

    const TEST_HANDLE_FILE_OVERRIDES = 'file_overrides';
    const TEST_HANDLE_BLOCK_OUTPUT = 'block_output';
    const TEST_HANDLE_PAGE_OUTPUT = 'page_output';

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(Runner $report): void
    {
        if ($this->config->get('concrete.cache.overrides')) {
            $report->success(
                'File override records are cached.',
                $report->button(new CacheSettingsLocation()),
                self::TEST_HANDLE_FILE_OVERRIDES
            );
        } else {
            $report->alert(
                'File override records are not cached!',
                $report->button(new CacheSettingsLocation()),
                self::TEST_HANDLE_FILE_OVERRIDES
            );
        }
        if ($this->config->get('concrete.cache.blocks')) {
            $report->success(
                'Block output caching is enabled by default.',
                $report->button(new CacheSettingsLocation()),
                self::TEST_HANDLE_BLOCK_OUTPUT
            );
        } else {
            $report->warning(
                'Block output caching is not enabled!!',
                $report->button(new CacheSettingsLocation()),
                self::TEST_HANDLE_BLOCK_OUTPUT
            );
        }
        if ($this->config->get('concrete.cache.assets')) {
            $report->info(
                'Asset caching has been enabled. This may have unpredictable results and may not be necessary unless a large amount of add-ons are used.',
                $report->button(new CacheSettingsLocation())
            );
        }
        if (!$this->config->get('concrete.cache.pages')) {
            $report->warning(
                'Full page cache has been explicitly disabled. Consider setting this to blocks or to all.',
                $report->button(new CacheSettingsLocation()),
                self::TEST_HANDLE_PAGE_OUTPUT
            );
        } else {
            if ($this->config->get('concrete.cache.pages') === 'all') {
                $report->success(
                    'Full page cache is enabled by default.',
                    $report->button(new CacheSettingsLocation()),
                    self::TEST_HANDLE_PAGE_OUTPUT
                );
            }
        }
    }

}
