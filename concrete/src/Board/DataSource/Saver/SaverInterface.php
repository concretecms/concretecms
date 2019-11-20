<?php

namespace Concrete\Core\Board\DataSource\Saver;

use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Filesystem\Element;
use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die("Access Denied.");

interface SaverInterface
{

    public function addConfiguredDataSourceFromRequest(
        Board $board, 
        DataSource $dataSource, 
        Request $request
    ): ConfiguredDataSource;

    public function updateConfiguredDataSourceFromRequest(
        ConfiguredDataSource $configuredDataSource,
        Request $request
    ): ConfiguredDataSource;

}
