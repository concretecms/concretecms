<?php

namespace Concrete\Tests\Config;

use Concrete\Controller\Dialog\Help\Help as HelpDialogController;
use Concrete\Controller\Panel\Help as HelpPanelController;
use Concrete\Tests\TestCase;

class HelpSystemConfigTest extends TestCase
{
    public function testDefaultValueIsEnabled()
    {
        $config = app('config');

        $this->assertTrue((bool) $config->get('concrete.accessibility.display_help_system'));
    }

    public function testHelpPanelControllerRespectsDisabledConfig()
    {
        $config = app('config');
        $original = $config->get('concrete.accessibility.display_help_system');

        try {
            $config->set('concrete.accessibility.display_help_system', true);
            $controller = app()->build(HelpPanelController::class);
            $this->assertTrue($controller->shouldRunControllerTask());

            $config->set('concrete.accessibility.display_help_system', false);
            $controller = app()->build(HelpPanelController::class);
            $this->assertFalse($controller->shouldRunControllerTask());
        } finally {
            $config->set('concrete.accessibility.display_help_system', $original);
        }
    }

    public function testHelpDialogControllerRespectsDisabledConfig()
    {
        $config = app('config');
        $original = $config->get('concrete.accessibility.display_help_system');

        try {
            $config->set('concrete.accessibility.display_help_system', false);
            $controller = app()->build(HelpDialogController::class);
            $this->assertFalse($controller->canAccess());
        } finally {
            $config->set('concrete.accessibility.display_help_system', $original);
        }
    }
}